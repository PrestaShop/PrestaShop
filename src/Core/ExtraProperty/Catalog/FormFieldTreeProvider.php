<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Throwable;

/**
 * Builds the field tree of a single back-office form by creating its form builder (without any
 * data) and walking the child builders recursively.
 *
 * Known limitation: fields added dynamically through form events (e.g. PRE_SET_DATA listeners)
 * do not exist on the builder yet, so they cannot appear in the tree. This is acceptable for the
 * autocomplete use case — the tree is a navigation aid, not an exhaustive contract.
 *
 * Recursion stops at {@see self::MAX_DEPTH} levels to keep the payload bounded (deeper nodes are
 * pruned); a child that cannot be introspected is logged and skipped without breaking its siblings.
 */
final class FormFieldTreeProvider implements FormFieldTreeProviderInterface
{
    /**
     * Maximum node depth of the returned tree (root fields are at depth 1).
     */
    private const MAX_DEPTH = 6;

    public function __construct(
        private readonly FormCatalogInterface $formCatalog,
        private readonly FormFactoryInterface $formFactory,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getTree(string $formId): ?array
    {
        $formTypeClass = $this->formCatalog->getFormTypeClass($formId);
        if (null === $formTypeClass) {
            return null;
        }

        try {
            $formBuilder = $this->formFactory->createBuilder($formTypeClass);
        } catch (Throwable $e) {
            $this->logger->warning(
                sprintf('Extra property form field tree: could not build form "%s" (%s): %s', $formId, $formTypeClass, $e->getMessage()),
                ['exception' => $e],
            );

            return null;
        }

        return $this->buildNodes($formBuilder, '', 1);
    }

    /**
     * @return list<FormFieldNode>
     */
    private function buildNodes(FormBuilderInterface $builder, string $parentPath, int $depth): array
    {
        $nodes = [];
        foreach ($builder->all() as $name => $childBuilder) {
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

    private function buildNode(string $name, FormBuilderInterface $childBuilder, string $parentPath, int $depth): FormFieldNode
    {
        $path = '' === $parentPath ? $name : $parentPath . '.' . $name;
        // getCompound() reflects the resolved "compound" option (applied on the builder by the base FormType)
        $compound = $childBuilder->getCompound();

        $children = [];
        if ($compound && $depth < self::MAX_DEPTH && count($childBuilder->all()) > 0) {
            $children = $this->buildNodes($childBuilder, $path, $depth + 1);
        }

        return new FormFieldNode(
            $name,
            $path,
            $this->resolveLabel($childBuilder, $name),
            get_class($childBuilder->getType()->getInnerType()),
            $compound,
            $children,
        );
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
