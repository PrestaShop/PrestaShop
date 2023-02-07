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

namespace PrestaShop\PrestaShop\Adapter\Order\Repository;

use Order;
use OrderDetail;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductCustomizationForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderDetailId;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderDetailCustomization;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderDetailCustomizations;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use PrestaShopException;
use Product;

class OrderRepository extends AbstractObjectModelRepository
{
    /**
     * Gets legacy Order
     *
     * @param OrderId $orderId
     *
     * @return Order
     *
     * @throws OrderException
     * @throws CoreException
     */
    public function get(OrderId $orderId): Order
    {
        try {
            $order = new Order($orderId->getValue());

            if ($order->id !== $orderId->getValue()) {
                throw new OrderNotFoundException($orderId, sprintf('%s #%d was not found', Order::class, $orderId->getValue()));
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf(
                    'Error occurred when trying to get %s #%d [%s]',
                    Order::class,
                    $orderId->getValue(),
                    $e->getMessage()
                ),
                0,
                $e
            );
        }

        return $order;
    }

    /**
     * @param OrderDetailId $detailId
     * @param LanguageId $languageId
     * @return OrderDetailCustomizations|null
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function getOrderDetailCustomizations(OrderDetailId $detailId, LanguageId $languageId): ?OrderDetailCustomizations
    {
        $orderDetail = new OrderDetail($detailId->getValue());
        $order = new Order($orderDetail->id_order);
        $customizations = [];
        $productCustomizations = Product::getAllCustomizedDatas($order->id_cart, $languageId->getValue(), true, $order->id_shop, $orderDetail->id_customization);
        $customizedDatas = $productCustomizations[$orderDetail->product_id][$orderDetail->product_attribute_id] ?? null;
        if (!is_array($customizedDatas)) {
            return null;
        }

        foreach ($customizedDatas as $customizationPerAddress) {
            foreach ($customizationPerAddress as $customization) {
                foreach ($customization['datas'] as $datas) {
                    foreach ($datas as $data) {
                        $customizations[] = new OrderProductCustomizationForViewing((int) $data['type'], $data['name'], $data['value']);
                    }
                }
            }
        }

        return new OrderDetailCustomizations($customizations);
    }
}
