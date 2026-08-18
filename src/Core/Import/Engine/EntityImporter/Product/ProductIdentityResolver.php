<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\EntityMatch;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ImportEntityExistenceChecker;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\AmbiguousReferenceException;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\ReferenceOutsideShopScopeException;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;

/**
 * Product identity lookups shared by the phases.
 *
 * resolve() is the option-gated update-vs-create decision (match_ref /
 * force IDs); findExistingByReferenceThenId() is the option-independent
 * lookup used to re-derive a product from its identity columns (association
 * phases — the row was already imported, so the run options must not gate
 * the lookup); resolveProductTarget() is the single decision point for
 * product association targets.
 */
class ProductIdentityResolver
{
    public const TARGET_MATCHED_BY_ID = 'id';
    public const TARGET_MATCHED_BY_REFERENCE = 'reference';

    public function __construct(
        protected readonly ProductRepository $productRepository,
        protected readonly ImportEntityExistenceChecker $existenceChecker,
    ) {
    }

    /**
     * Update-vs-create decision, legacy productImportOne parity:
     * 1. match_ref on AND the reference matches an existing product (shop-scoped
     *    lookup) -> update that product;
     * 2. else force IDs on AND id present AND the product exists -> update it;
     * 3. else create — with the forced id when force IDs is on and id is present.
     * With force IDs off the id column is ignored entirely (legacy behavior kept).
     *
     * @param array<string, string> $row mapped row values
     *
     * @throws AmbiguousReferenceException when match_ref is on and the reference
     *                                     matches SEVERAL in-scope products — updating an arbitrary one of them
     *                                     is destructive (validation normally already skipped the row; this is
     *                                     the database-phase defense)
     * @throws ReferenceOutsideShopScopeException when match_ref is on and the
     *                                            reference exists in the catalog but on none of the run's shops —
     *                                            creating would duplicate the reference (validation normally already
     *                                            skipped the row; this is the database-phase defense)
     */
    public function resolve(array $row, ImportRunContext $context): EntityMatch
    {
        $options = $context->getOptions();

        $reference = $row['reference'] ?? '';
        if ($options->matchRef && '' !== $reference) {
            $productIds = $this->productRepository->getProductIdsByReference($reference, $context->getShopConstraint());
            if (count($productIds) > 1) {
                throw new AmbiguousReferenceException($reference, count($productIds));
            }
            if ([] !== $productIds) {
                return new EntityMatch($productIds[0], EntityMatch::MATCHED_BY_REFERENCE);
            }
            if ($this->referenceExistsOutsideScope($reference, $context)) {
                throw new ReferenceOutsideShopScopeException(sprintf('The reference "%s" matches a product outside the run\'s shop scope; the row was skipped to avoid creating a duplicate product.', $reference));
            }
        }

        $id = $row['id'] ?? '';
        if ($options->forceIds && ctype_digit($id) && (int) $id > 0) {
            if ($this->existenceChecker->exists('product', (int) $id)) {
                return new EntityMatch((int) $id, EntityMatch::MATCHED_BY_ID);
            }

            return new EntityMatch(null, null, (int) $id);
        }

        return new EntityMatch(null);
    }

    /**
     * Whether the reference misses within the run's shop scope but hits in the
     * whole catalog — the case where a match_ref creation would duplicate the
     * reference on another shop.
     */
    public function referenceExistsOutsideScope(string $reference, ImportRunContext $context): bool
    {
        $shopConstraint = $context->getShopConstraint();
        if ('' === $reference || $shopConstraint->forAllShops()) {
            return false;
        }

        return [] === $this->productRepository->getProductIdsByReference($reference, $shopConstraint)
            && [] !== $this->productRepository->getProductIdsByReference($reference, ShopConstraint::allShops());
    }

    /**
     * How many in-scope products carry the reference. Used by the validator to
     * report the ambiguity as a row error in the pausing validation phase, before
     * anything is written.
     */
    public function countProductsByReference(string $reference, ImportRunContext $context): int
    {
        if ('' === $reference) {
            return 0;
        }

        return count($this->productRepository->getProductIdsByReference($reference, $context->getShopConstraint()));
    }

    /**
     * Option-independent existing-product lookup: reference first (shop-scoped),
     * then the id when one is provided; a no-match EntityMatch when neither does.
     *
     * Several products may carry the reference; entityId is then the lowest id
     * and matchCount reports how many matched, so the caller can warn. This is
     * the association path, where the row is already written — an ambiguous
     * owner or target only affects the link, so it warns instead of failing
     * (contrast resolve(), which throws AmbiguousReferenceException).
     */
    public function findExistingByReferenceThenId(string $reference, ?int $productId, ImportRunContext $context): EntityMatch
    {
        if ('' !== $reference) {
            $existingIds = $this->productRepository->getProductIdsByReference($reference, $context->getShopConstraint());
            if ([] !== $existingIds) {
                return new EntityMatch($existingIds[0], EntityMatch::MATCHED_BY_REFERENCE, null, count($existingIds));
            }
        }

        if (null !== $productId && $this->existenceChecker->exists('product', $productId)) {
            return new EntityMatch($productId, EntityMatch::MATCHED_BY_ID);
        }

        return new EntityMatch(null);
    }

    /**
     * Single decision point for one product association target (accessories
     * today; any future product-to-product association — e.g. pack contents —
     * should resolve its targets here too), shared by the
     * association_validation and association phases so both always agree.
     * A NUMERIC target is treated as a product id first, but may equally be
     * a product reference that merely looks numeric: the id match wins and
     * the coincidence is flagged as ambiguous; otherwise the target resolves
     * by reference. Callers only choose message wording and severity.
     *
     * Two INDEPENDENT kinds of ambiguity are reported, so callers can word them
     * differently:
     * - 'ambiguous': the numeric target matches both a product id AND some
     *   product's reference (id wins);
     * - 'referenceMatchCount' > 1: the reference itself matches several products
     *   (the lowest id is used).
     *
     * @return array{resolvedId: ?int, matchedBy: self::TARGET_MATCHED_BY_*|null, ambiguous: bool, referenceMatchCount: int}
     */
    public function resolveProductTarget(string $target, ImportRunContext $context): array
    {
        $referenceMatchIds = $this->productRepository->getProductIdsByReference($target, $context->getShopConstraint());
        $referenceMatchCount = count($referenceMatchIds);

        if (ctype_digit($target)) {
            if ($this->existenceChecker->exists('product', (int) $target)) {
                return [
                    'resolvedId' => (int) $target,
                    'matchedBy' => self::TARGET_MATCHED_BY_ID,
                    'ambiguous' => [] !== $referenceMatchIds,
                    // the id won, so the reference multiplicity is irrelevant here
                    'referenceMatchCount' => 0,
                ];
            }
        }

        return [
            'resolvedId' => $referenceMatchIds[0] ?? null,
            'matchedBy' => [] !== $referenceMatchIds ? self::TARGET_MATCHED_BY_REFERENCE : null,
            'ambiguous' => false,
            'referenceMatchCount' => $referenceMatchCount,
        ];
    }
}
