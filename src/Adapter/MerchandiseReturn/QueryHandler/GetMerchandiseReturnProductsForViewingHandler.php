<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\MerchandiseReturn\QueryHandler;

use Order;
use PrestaShop\PrestaShop\Adapter\MerchandiseReturn\AbstractMerchandiseReturnHandler;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Query\GetMerchandiseReturnProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryHandler\GetMerchandiseReturnProductsForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryResult\EditableMerchandiseReturn;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryResult\MerchandiseReturnProductForViewing;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryResult\MerchandiseReturnProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductCustomizationForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductCustomizationsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Handles query which gets merchandise return for editing
 */
final class GetMerchandiseReturnProductsForViewingHandler extends AbstractMerchandiseReturnHandler implements GetMerchandiseReturnProductsForViewingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetMerchandiseReturnProductsForViewing $query): MerchandiseReturnProductsForViewing
    {
        $merchandiseReturnId = $query->getMerchandiseReturnId();
        $orderReturn = $this->getOrderReturn($merchandiseReturnId);
        $order = new Order($orderReturn->id_order);

        $products = \OrderReturn::getOrdersReturnProducts($orderReturn->id, $order);

        $productsForViewing = [];

        foreach ($products as $product) {
            // Get total customized quantity for current product
            $customized_product_quantity = 0;
            $customizations = [];
            if (is_array($product['customizedDatas'])) {
                foreach ($product['customizedDatas'] as $customizationPerAddress) {
                    foreach ($customizationPerAddress as $customizationId => $customization) {
                        $customized_product_quantity += (int) $customization['quantity'];
                        foreach ($customization['datas'] as $datas) {
                            foreach ($datas as $data) {
                                $customizations[] = new OrderProductCustomizationForViewing((int) $data['type'], $data['name'], $data['value']);
                            }
                        }
                    }
                }
            }

            $productsForViewing[] = new MerchandiseReturnProductForViewing(
                new ProductId((int) $product['product_id']),
                (int) $product['id_order_detail'],
                $product['reference'],
                $product['product_name'],
                (int) $product['product_quantity'],
                !empty($customizations) ? new OrderProductCustomizationsForViewing($customizations) : null
            );
        }

        return new MerchandiseReturnProductsForViewing($productsForViewing);
    }
}
