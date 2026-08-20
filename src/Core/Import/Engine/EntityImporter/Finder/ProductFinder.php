<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ImportEntityExistenceChecker;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;

/**
 * Product identity lookups shared by the phases — MATCH-ONLY: this finder
 * never creates anything and never decides severity; it returns data and the
 * callers own the interpretation (see the ambiguity policy in the Import
 * PLAN.md).
 *
 * findRowMatch() answers the option-gated update-vs-create question
 * (match_ref / force IDs); findByReferenceThenId() is the option-independent
 * lookup used to re-derive a product from its identity columns (association
 * phases — the row was already imported, so the run options must not gate
 * the lookup); findTarget() is the single decision point for product
 * association targets.
 */
class ProductFinder
{
    public function __construct(
        protected readonly ProductRepository $productRepository,
        protected readonly ImportEntityExistenceChecker $existenceChecker,
    ) {
    }

    /**
     * Update-vs-create match, legacy productImportOne parity:
     * 1. match_ref on AND the reference matches an existing product (shop-scoped
     *    lookup) -> update that product;
     * 2. else force IDs on AND id present AND the product exists -> update it;
     * 3. else create — with the forced id when force IDs is on and id is present.
     * With force IDs off the id column is ignored entirely (legacy behavior kept).
     *
     * Two states the CALLERS must treat as row errors before writing anything
     * (both signalled as data, never thrown):
     * - isAmbiguous(): the reference matches SEVERAL in-scope products —
     *   updating an arbitrary one of them is destructive, so first() must not
     *   be used as an update target;
     * - foundOutsideShopScope: the reference exists in the catalog but on
     *   none of the run's shops — creating would duplicate it.
     *
     * @param array<string, string> $row mapped row values
     */
    public function findRowMatch(array $row, ImportRunContext $context): EntityLookupResult
    {
        $options = $context->getOptions();

        $reference = $row['reference'] ?? '';
        if ($options->matchRef && '' !== $reference) {
            $productIds = $this->productRepository->getProductIdsByReference($reference, $context->getShopConstraint());
            if ([] !== $productIds) {
                return new EntityLookupResult($this->toMatches($productIds, EntityLookupResult::MATCHED_BY_REFERENCE));
            }
            if ($this->referenceExistsOutsideScope($reference, $context)) {
                return new EntityLookupResult([], null, true);
            }
        }

        $id = $row['id'] ?? '';
        if ($options->forceIds && ctype_digit($id) && (int) $id > 0) {
            if ($this->existenceChecker->exists('product', (int) $id)) {
                return new EntityLookupResult([['id' => (int) $id, 'matchedBy' => EntityLookupResult::MATCHED_BY_ID]]);
            }

            return new EntityLookupResult([], (int) $id);
        }

        return new EntityLookupResult([]);
    }

    /**
     * Option-independent existing-product lookup: reference first (shop-scoped),
     * then the id when one is provided; an empty result when neither matches.
     *
     * Several products may carry the reference; ids are then every match
     * (lowest first) so the caller can warn. This is the association path,
     * where the row is already written — an ambiguous owner or target only
     * affects the link, so callers warn instead of failing (contrast
     * findRowMatch(), where ambiguity forbids choosing a target).
     */
    public function findByReferenceThenId(string $reference, ?int $productId, ImportRunContext $context): EntityLookupResult
    {
        if ('' !== $reference) {
            $existingIds = $this->productRepository->getProductIdsByReference($reference, $context->getShopConstraint());
            if ([] !== $existingIds) {
                return new EntityLookupResult($this->toMatches($existingIds, EntityLookupResult::MATCHED_BY_REFERENCE));
            }
        }

        if (null !== $productId && $this->existenceChecker->exists('product', $productId)) {
            return new EntityLookupResult([['id' => $productId, 'matchedBy' => EntityLookupResult::MATCHED_BY_ID]]);
        }

        return new EntityLookupResult([]);
    }

    /**
     * Single decision point for one product association target (accessories
     * today; any future product-to-product association — e.g. pack contents —
     * should resolve its targets here too), shared by the
     * association_validation and association phases so both always agree.
     * A NUMERIC target is treated as a product id first, but may equally be
     * a product reference that merely looks numeric: the id match wins (it
     * comes first in the matches) and any reference matches follow it — so
     * firstMatchedBy() === MATCHED_BY_ID with isAmbiguous() is the
     * id/reference collision, while firstMatchedBy() === MATCHED_BY_REFERENCE
     * with isAmbiguous() is a plain multi-product reference. Callers only
     * choose message wording and severity.
     */
    public function findTarget(string $target, ImportRunContext $context): EntityLookupResult
    {
        $referenceMatches = $this->toMatches(
            $this->productRepository->getProductIdsByReference($target, $context->getShopConstraint()),
            EntityLookupResult::MATCHED_BY_REFERENCE
        );

        if (ctype_digit($target) && $this->existenceChecker->exists('product', (int) $target)) {
            return new EntityLookupResult([
                ['id' => (int) $target, 'matchedBy' => EntityLookupResult::MATCHED_BY_ID],
                ...$referenceMatches,
            ]);
        }

        return new EntityLookupResult($referenceMatches);
    }

    /**
     * @param list<int> $ids
     *
     * @return list<array{id: int, matchedBy: string}>
     */
    protected function toMatches(array $ids, string $matchedBy): array
    {
        return array_map(static fn (int $id): array => ['id' => $id, 'matchedBy' => $matchedBy], $ids);
    }

    /**
     * Whether the reference misses within the run's shop scope but hits in the
     * whole catalog — the case where a match_ref creation would duplicate the
     * reference on another shop.
     */
    protected function referenceExistsOutsideScope(string $reference, ImportRunContext $context): bool
    {
        $shopConstraint = $context->getShopConstraint();
        if ('' === $reference || $shopConstraint->forAllShops()) {
            return false;
        }

        return [] === $this->productRepository->getProductIdsByReference($reference, $shopConstraint)
            && [] !== $this->productRepository->getProductIdsByReference($reference, ShopConstraint::allShops());
    }
}
