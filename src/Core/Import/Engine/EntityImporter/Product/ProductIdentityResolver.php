<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\Repository\ProductLookup;

/**
 * Update-vs-create decision, legacy productImportOne parity:
 * 1. match_ref on AND the reference matches an existing product (shop-scoped
 *    lookup) -> update that product;
 * 2. else force IDs on AND id present AND the product exists -> update it;
 * 3. else create — with the forced id when force IDs is on and id is present.
 * With force IDs off the id column is ignored entirely (legacy behavior kept).
 */
final class ProductIdentityResolver
{
    public function __construct(
        private readonly ProductLookup $productLookup,
    ) {
    }

    /**
     * @param array<string, string> $row mapped row values
     */
    public function resolve(array $row, ImportRunContext $context): ProductMatch
    {
        $options = $context->getOptions();

        $reference = $row['reference'] ?? '';
        if ($options->matchRef && '' !== $reference) {
            $productId = $this->productLookup->getProductIdByReference($reference, $context->getShopId());
            if (null !== $productId) {
                return new ProductMatch($productId, ProductMatch::MATCHED_BY_REFERENCE);
            }
        }

        $id = $row['id'] ?? '';
        if ($options->forceIds && ctype_digit($id) && (int) $id > 0) {
            if ($this->productLookup->productExists((int) $id)) {
                return new ProductMatch((int) $id, ProductMatch::MATCHED_BY_ID);
            }

            return new ProductMatch(null, null, (int) $id);
        }

        return new ProductMatch(null);
    }
}
