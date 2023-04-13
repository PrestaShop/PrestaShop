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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\QueryHandler;

use Order;
use PrestaShop\PrestaShop\Adapter\Entity\OrderDetail;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderDetailCustomizations;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryHandler\GetOrderDetailCustomizationsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderDetailCustomization;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderDetailCustomizations;
use Product;

class GetOrderDetailCustomizationsHandler implements GetOrderDetailCustomizationsHandlerInterface
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * GetOrderDetailCustomizationsHandler constructor.
     *
     * @param int $contextLangId
     */
    public function __construct(
        int $contextLangId
    ) {
        $this->contextLangId = $contextLangId;
    }

    /**
     * @param GetOrderDetailCustomizations $query
     *
     * @return OrderDetailCustomizations|null
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function handle(GetOrderDetailCustomizations $query): ?OrderDetailCustomizations
    {
        $orderDetail = new OrderDetail($query->getOrderDetailId()->getValue());
        $order = new Order($orderDetail->id_order);
        $customizations = [];
        $productCustomizations = Product::getAllCustomizedDatas($order->id_cart, $this->contextLangId, true, $order->id_shop, $orderDetail->id_customization);
        $customizedDatas = $productCustomizations[$orderDetail->product_id][$orderDetail->product_attribute_id] ?? null;
        if (!is_array($customizedDatas)) {
            return null;
        }

        foreach ($customizedDatas as $customizationPerAddress) {
            foreach ($customizationPerAddress as $customizationId => $customization) {
                foreach ($customization['datas'] as $datas) {
                    foreach ($datas as $data) {
                        $customizations[] = new OrderDetailCustomization((int) $data['type'], $data['name'], $data['value']);
                    }
                }
            }
        }

        return new OrderDetailCustomizations($customizations);
    }
}
