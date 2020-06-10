<?php

namespace PrestaShop\PrestaShop\Adapter\MerchandiseReturn\QueryHandler;

use Order;
use PrestaShop\PrestaShop\Adapter\Entity\OrderDetail;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Query\GetOrderDetailCustomization;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryHandler\GetOrderDetailCustomizationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryResult\OrderDetailCustomization;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryResult\OrderDetailCustomizations;
use Product;

class GetOrderDetailCustomizationHandler implements GetOrderDetailCustomizationHandlerInterface
{
    /**
     * @param GetOrderDetailCustomization $query
     *
     * @return OrderDetailCustomizations|null
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function handle(GetOrderDetailCustomization $query): ?OrderDetailCustomizations
    {
        $orderDetail = new OrderDetail($query->getOrderDetailId());
        $order = new Order($orderDetail->id_order);
        $customizations = [];
        /** @todo need id lang */
        $productCustomizations = Product::getAllCustomizedDatas($order->id_cart, 1, true, null, $orderDetail->id_customization);
        $customizedDatas = null;
        if (isset($productCustomizations[$orderDetail->product_id][$orderDetail->product_attribute_id])) {
            $customizedDatas = $productCustomizations[$orderDetail->product_id][$orderDetail->product_attribute_id];
        }
        if (is_array($customizedDatas)) {
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
        return null;
    }
}
