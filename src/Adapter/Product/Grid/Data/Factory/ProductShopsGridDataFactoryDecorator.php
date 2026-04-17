<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;
use Shop;

/**
 * Decorates product grid data, but instead of adding details of all shops for the associated column, we prepare detail
 * for single shop on each row.
 */
class ProductShopsGridDataFactoryDecorator extends ProductGridDataFactoryDecorator
{
    /**
     * @param array $products
     * @param ShopSearchCriteriaInterface $searchCriteria
     *
     * @return array<int, array<string, mixed>>
     */
    protected function applyShopModifications(array $products, ShopSearchCriteriaInterface $searchCriteria): array
    {
        foreach ($products as $i => $product) {
            // Transform list of IDs into list of names
            if (!empty($product['id_shop'])) {
                $shop = $this->shopRepository->get(new ShopId((int) $product['id_shop']));
                $products[$i]['shop_name'] = $shop->name;
                $products[$i]['shop_color'] = $shop->color;
            }
        }

        return $products;
    }
}
