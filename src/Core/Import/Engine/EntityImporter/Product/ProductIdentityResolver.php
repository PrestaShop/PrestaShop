<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\EntityMatch;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;

/**
 * Product identity lookups shared by the phases.
 *
 * resolve() is the option-gated update-vs-create decision (match_ref /
 * force IDs); findExistingByReferenceThenId() is the option-independent
 * lookup used to re-derive a product from its identity columns (association
 * phase — the row was already imported, so the run options must not gate
 * the lookup).
 */
class ProductIdentityResolver
{
    public function __construct(
        protected readonly ProductRepository $productRepository,
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
     */
    public function resolve(array $row, ImportRunContext $context): EntityMatch
    {
        $options = $context->getOptions();

        $reference = $row['reference'] ?? '';
        if ($options->matchRef && '' !== $reference) {
            $productId = $this->productRepository->getProductIdByReference($reference, $context->getShopId());
            if (null !== $productId) {
                return new EntityMatch($productId, EntityMatch::MATCHED_BY_REFERENCE);
            }
        }

        $id = $row['id'] ?? '';
        if ($options->forceIds && ctype_digit($id) && (int) $id > 0) {
            if ($this->productExists((int) $id)) {
                return new EntityMatch((int) $id, EntityMatch::MATCHED_BY_ID);
            }

            return new EntityMatch(null, null, (int) $id);
        }

        return new EntityMatch(null);
    }

    /**
     * Option-independent existing-product lookup: reference first (shop-scoped),
     * then the id when one is provided. Returns null when neither matches.
     */
    public function findExistingByReferenceThenId(string $reference, ?int $productId, int $shopId): ?int
    {
        if ('' !== $reference) {
            $existingId = $this->productRepository->getProductIdByReference($reference, $shopId);
            if (null !== $existingId) {
                return $existingId;
            }
        }

        if (null !== $productId && $this->productExists($productId)) {
            return $productId;
        }

        return null;
    }

    /**
     * Both catalog probes for a NUMERIC association target, which may be a
     * product id OR a product reference that merely looks numeric. The caller
     * decides (id wins) and reports ambiguity — see the accessories pre-check
     * and the association phase.
     *
     * @return array{idExists: bool, referenceMatchId: ?int}
     */
    public function classifyNumericTarget(int $numericTarget, int $shopId): array
    {
        return [
            'idExists' => $this->productExists($numericTarget),
            'referenceMatchId' => $this->productRepository->getProductIdByReference((string) $numericTarget, $shopId),
        ];
    }

    protected function productExists(int $productId): bool
    {
        if ($productId <= 0) {
            return false;
        }

        try {
            $this->productRepository->assertProductExists(new ProductId($productId));
        } catch (ProductNotFoundException) {
            return false;
        }

        return true;
    }
}
