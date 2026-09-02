<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShopBundle\Form\Admin\Sell\Product\EditProductFormType;
use PrestaShopBundle\Form\Admin\Type\TranslatableChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Throwable;

/**
 * Builds the field tree of a single back-office form by creating its form builder (without any
 * data) and walking the child builders recursively. Lazily called per form (one form at a time —
 * building every form eagerly would be far too expensive), see
 * ExtraPropertyDefinitionController::formFieldsAction().
 *
 * Known limitation: fields added dynamically through form events (e.g. PRE_SET_DATA listeners)
 * do not exist on the builder yet, so they cannot appear in the tree. This is acceptable for the
 * autocomplete use case — the tree is a navigation aid, not an exhaustive contract.
 *
 * Recursion stops at {@see self::MAX_DEPTH} levels to keep the payload bounded (deeper nodes are
 * pruned); a child that cannot be introspected is logged and skipped without breaking its siblings.
 * Trees are cached per form in the prestashop.extra_property.catalog.filesystem_cache pool.
 *
 * @phpstan-type FieldNode array{name: string, path: string, label: string, compound: bool, children: list<mixed>}
 */
class FormFieldTreeProvider
{
    /**
     * Maximum node depth of the returned tree (root fields are at depth 1).
     */
    private const MAX_DEPTH = 6;

    /**
     * Types (matched anywhere in the resolved type hierarchy) whose children are internal
     * machinery rather than placeable fields: a translatable field's per-language inputs, a
     * choice's expanded options, a collection's dynamic entries. The node itself stays a valid
     * before/after anchor — it just stops the recursion and reads as a leaf.
     */
    private const LEAF_TYPES = [
        ChoiceType::class,
        CollectionType::class,
        TranslatableChoiceType::class,
        TranslatableType::class,
        TranslateType::class,
    ];

    public function __construct(
        private readonly FormCatalog $formCatalog,
        private readonly FormFactoryInterface $formFactory,
        private readonly LoggerInterface $logger,
        private readonly CacheItemPoolInterface $cache,
        private readonly ShopContext $shopContext,
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * @param string $formId a form id known to the form catalog (form type block prefix)
     *
     * @return list<FieldNode>|null the root fields of the form, or null when the form id is
     *                              unknown or the form cannot be built
     */
    public function getTree(string $formId): ?array
    {
        $formTypeClass = $this->formCatalog->getFormTypeClass($formId);
        if (null === $formTypeClass) {
            return null;
        }

        // Cached per form in the extra property catalog pool (cleared with the Symfony cache,
        // which module management already triggers); an un-introspectable form (null) is never
        // cached, so a transient failure is retried on the next request. The form id is a block
        // prefix ([A-Za-z0-9_]+), so it is PSR-6-safe as-is.
        $item = $this->cache->getItem('form_fields_' . $formId);
        if ($item->isHit()) {
            return $item->get();
        }

        try {
            $formBuilder = $this->formFactory->createBuilder($formTypeClass, null, $this->introspectionOptions($formId, $formTypeClass));

            // Child builders resolve lazily: the first buildNodes() level is what actually runs
            // the children's buildForm(), so it must sit inside this try too.
            $tree = $this->buildNodes($formBuilder, '', 1);
            $this->cache->save($item->set($tree));

            return $tree;
        } catch (Throwable $e) {
            $this->logger->warning(
                sprintf('Extra property form field tree: could not build form "%s" (%s): %s', $formId, $formTypeClass, $e->getMessage()),
                ['exception' => $e],
            );

            return null;
        }
    }

    /**
     * Options for the throwaway introspection build of a form type with REQUIRED options.
     *
     * The product edit form — the most commonly targeted form of the BO — is the one known such
     * type, so its exception is handled inline. The values only need to make the form BUILD, but
     * the product id must point at a REAL product: FooterType generates the preview/FO links at
     * build time and the legacy Link class rejects a product it cannot link to. The standard
     * product type exposes the common field set, the shop comes from the context. On a catalog
     * with no product at all the build fails and the form gracefully reads as not introspectable.
     *
     * If more forms with required options ever need introspection, this is the seam to
     * generalize: a tagged "introspection options provider" collection (supports($formId,
     * $formTypeClass) + getOptions()) injected as an iterable, with this product case as its
     * first implementation.
     *
     * @return array<string, mixed>
     */
    private function introspectionOptions(string $formId, string $formTypeClass): array
    {
        if (is_a($formTypeClass, EditProductFormType::class, true)) {
            return [
                // Constrained to the current shop: on a multishop setup the catalog-wide lowest
                // id may not be associated to this shop, and FooterType's link generation would
                // reject it — reading the whole tree as not introspectable.
                'product_id' => (int) $this->connection->fetchOne(
                    'SELECT MIN(ps.id_product) FROM ' . $this->dbPrefix . 'product_shop ps WHERE ps.id_shop = :shopId',
                    ['shopId' => $this->shopContext->getId()]
                ),
                'shop_id' => $this->shopContext->getId(),
                'product_type' => ProductType::TYPE_STANDARD,
                'tax_rules_group_id' => 0,
            ];
        }

        return [];
    }

    /**
     * @return list<FieldNode>
     */
    private function buildNodes(FormBuilderInterface $builder, string $parentPath, int $depth): array
    {
        $nodes = [];
        foreach ($builder->all() as $name => $childBuilder) {
            // Underscore-prefixed children are internal machinery (_toolbar_buttons, _token…),
            // not placeable fields.
            if (str_starts_with((string) $name, '_')) {
                continue;
            }

            try {
                $nodes[] = $this->buildNode((string) $name, $childBuilder, $parentPath, $depth);
            } catch (Throwable $e) {
                $this->logger->warning(
                    sprintf('Extra property form field tree: skipped field "%s" of "%s": %s', $name, $parentPath, $e->getMessage()),
                    ['exception' => $e],
                );
            }
        }

        return $nodes;
    }

    /**
     * @return FieldNode
     */
    private function buildNode(string $name, FormBuilderInterface $childBuilder, string $parentPath, int $depth): array
    {
        $path = '' === $parentPath ? $name : $parentPath . '.' . $name;
        // getCompound() reflects the resolved "compound" option (applied on the builder by the base FormType)
        $compound = $childBuilder->getCompound() && !$this->isLeafType($childBuilder);

        $children = [];
        if ($compound && $depth < self::MAX_DEPTH && count($childBuilder->all()) > 0) {
            $children = $this->buildNodes($childBuilder, $path, $depth + 1);
        }

        // No typeClass in the payload: the field FQCN is a server-side implementation detail the
        // client must not leak (same policy as FormCatalog::getAll()) — the picker only needs
        // name/path/label/compound.
        return [
            'name' => $name,
            'path' => $path,
            'label' => $this->resolveLabel($childBuilder, $name),
            'compound' => $compound,
            'children' => $children,
        ];
    }

    /**
     * Walks the resolved type hierarchy (a custom type "extends" another through getParent(),
     * not through class inheritance — e.g. MaterialChoiceTableType resolves to ChoiceType).
     */
    private function isLeafType(FormBuilderInterface $childBuilder): bool
    {
        $resolved = $childBuilder->getType();
        while (null !== $resolved) {
            if (in_array(get_class($resolved->getInnerType()), self::LEAF_TYPES, true)) {
                return true;
            }
            $resolved = $resolved->getParent();
        }

        return false;
    }

    private function resolveLabel(FormBuilderInterface $childBuilder, string $name): string
    {
        $label = $childBuilder->getOption('label');
        if (is_string($label) && '' !== $label) {
            return $label;
        }

        return ucfirst(str_replace('_', ' ', $name));
    }
}
