<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
