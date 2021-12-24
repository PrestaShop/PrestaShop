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

namespace PrestaShop\PrestaShop\Adapter\Order\QueryHandler;

use Currency;
use Db;
use Image;
use ImageManager;
use Order;
use OrderInvoice;
use OrderReturn;
use OrderSlip;
use Pack;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler\GetOrderProductsForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductCustomizationForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductCustomizationsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\QuerySorting;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use Product;
use Shop;
use StockAvailable;
use Warehouse;
use WarehouseProductLocation;

/**
 * Handles GetOrderProductsForViewing query using legacy object models
 */
final class GetOrderProductsForViewingHandler extends AbstractOrderHandler implements GetOrderProductsForViewingHandlerInterface
{
    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @var Locale
     */
    private $locale;

    public function __construct(
        ImageTagSourceParserInterface $imageTagSourceParser,
        int $contextLanguageId,
        Locale $locale
    ) {
        $this->imageTagSourceParser = $imageTagSourceParser;
        $this->contextLanguageId = $contextLanguageId;
        $this->locale = $locale;
    }

    public function handle(GetOrderProductsForViewing $query): OrderProductsForViewing
    {
        $order = $this->getOrder($query->getOrderId());
        $taxCalculationMethod = $this->getOrderTaxCalculationMethod($order);

        $products = $order->getProducts();
        $currency = new Currency((int) $order->id_currency);
        $computingPrecision = new ComputingPrecision();
        $precision = $computingPrecision->getPrecision($currency->precision);

        foreach ($products as &$product) {
            if ($product['image'] instanceof Image) {
                $name = 'product_mini_' . (int) $product['product_id'] . (isset($product['product_attribute_id']) ? '_' . (int) $product['product_attribute_id'] : '') . '.jpg';
                // generate image cache, only for back office
                $product['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_ . 'p/' . $product['image']->getExistingImgPath() . '.jpg', $name, 45, 'jpg');
                if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                    $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                } else {
                    $product['image_size'] = false;
                }
            }

            // Get total customized quantity for current product
            $customized_product_quantity = 0;

            $customizations = [];
            if (is_array($product['customizedDatas'])) {
                foreach ($product['customizedDatas'] as $customizationPerAddress) {
                    foreach ($customizationPerAddress as $customization) {
                        $customized_product_quantity += (int) $customization['quantity'];
                        foreach ($customization['datas'] as $datas) {
                            foreach ($datas as $data) {
                                $customizations[] = new OrderProductCustomizationForViewing(
                                    (int) $data['type'],
                                    (string) $data['name'],
                                    $data['value']
                                );
                            }
                        }
                    }
                }
            }

            $product['customizations'] = !empty($customizations) ? new OrderProductCustomizationsForViewing($customizations) : null;
            $product['customized_product_quantity'] = $customized_product_quantity;
            $product['current_stock'] = StockAvailable::getQuantityAvailableByProduct($product['product_id'], $product['product_attribute_id'], $product['id_shop']);
            $product['quantity_refundable'] = $product['product_quantity'] - $product['product_quantity_return'] - $product['product_quantity_refunded'];
            $product['amount_refundable'] = $product['total_price_tax_excl'] - $product['total_refunded_tax_excl'];
            $product['amount_refundable_tax_incl'] = $product['total_price_tax_incl'] - $product['total_refunded_tax_incl'];
            $product['displayed_max_refundable'] = $taxCalculationMethod === PS_TAX_EXC ? $product['amount_refundable'] : $product['amount_refundable_tax_incl'];
            $resumeAmountKey = $taxCalculationMethod === PS_TAX_EXC ? 'total_refunded_tax_excl' : 'total_refunded_tax_incl';
            $product['amount_refunded'] = $product[$resumeAmountKey] ?? 0;
            $product['refund_history'] = OrderSlip::getProductSlipDetail($product['id_order_detail']);
            $product['return_history'] = OrderReturn::getProductReturnDetail($product['id_order_detail']);

            if ($product['id_warehouse'] != 0) {
                $warehouse = new Warehouse((int) $product['id_warehouse']);
                $product['warehouse_name'] = $warehouse->name;
                $warehouse_location = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
                if (!empty($warehouse_location)) {
                    $product['warehouse_location'] = $warehouse_location;
                } else {
                    $product['warehouse_location'] = false;
                }
            } else {
                $product['warehouse_name'] = '--';
                $product['warehouse_location'] = false;
            }

            $pack_items = $product['cache_is_pack'] ? Pack::getItemTable($product['id_product'], $this->contextLanguageId, true) : [];
            foreach ($pack_items as &$pack_item) {
                $pack_item['current_stock'] = StockAvailable::getQuantityAvailableByProduct($pack_item['id_product'], $pack_item['id_product_attribute'], $pack_item['id_shop']);
                $this->setProductImageInformation($pack_item);
                if ($pack_item['image'] instanceof Image) {
                    $name = 'product_mini_' . (int) $pack_item['id_product'] . (isset($pack_item['id_product_attribute']) ? '_' . (int) $pack_item['id_product_attribute'] : '') . '.jpg';
                    // generate image cache, only for back office
                    $pack_item['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_ . 'p/' . $pack_item['image']->getExistingImgPath() . '.jpg', $name, 45, 'jpg');
                    if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                        $pack_item['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                    } else {
                        $pack_item['image_size'] = false;
                    }
                }
            }

            unset($pack_item);

            $product['pack_items'] = $pack_items;
        }

        unset($product);

        if (QuerySorting::DESC === $query->getProductsSorting()->getValue()) {
            // reorder products by order_detail_id DESC
            krsort($products);
        } else {
            // reorder products by order_detail_id ASC
            ksort($products);
        }

        $productsForViewing = [];

        $isOrderTaxExcluded = ($taxCalculationMethod == PS_TAX_EXC);
        foreach ($products as $product) {
            $unitPrice = $isOrderTaxExcluded ?
                $product['unit_price_tax_excl'] :
                $product['unit_price_tax_incl']
            ;

            // if rounding type is set to "per item" we must round the unit price now, otherwise values won't match
            // the totals in the order summary
            if ((int) $order->round_type === Order::ROUND_ITEM) {
                $unitPrice = (new DecimalNumber((string) $unitPrice))->round($precision, $this->getNumberRoundMode());
            }

            $totalPrice = $unitPrice *
                (!empty($product['customizedDatas']) ? $product['customizationQuantityTotal'] : $product['product_quantity']);

            $unitPriceFormatted = $this->locale->formatPrice($unitPrice, $currency->iso_code);
            $totalPriceFormatted = $this->locale->formatPrice($totalPrice, $currency->iso_code);

            $imagePath = isset($product['image_tag']) ?
                $this->imageTagSourceParser->parse($product['image_tag']) :
                null;
            $product['product_quantity_refunded'] = $product['product_quantity_refunded'] ?: false;

            $productType = !empty($product['pack_items']) ? OrderProductForViewing::TYPE_PACK :
                OrderProductForViewing::TYPE_PRODUCT_WITHOUT_COMBINATIONS;

            $orderInvoice = new OrderInvoice($product['id_order_invoice']);

            $packItems = [];
            foreach ($product['pack_items'] as $pack_item) {
                $packItemType = !empty($pack_item['pack_items']) ? OrderProductForViewing::TYPE_PACK :
                    OrderProductForViewing::TYPE_PRODUCT_WITHOUT_COMBINATIONS;
                $packItemImagePath = isset($pack_item['image_tag']) ?
                    $this->imageTagSourceParser->parse($pack_item['image_tag']) :
                    null;
                $packItems[] = new OrderProductForViewing(
                    null,
                    $pack_item['id_product'],
                    0,
                    $pack_item['name'],
                    $pack_item['reference'],
                    $pack_item['supplier_reference'],
                    $pack_item['pack_quantity'],
                    '0',
                    '0',
                    $pack_item['current_stock'],
                    $packItemImagePath,
                    '0',
                    '0',
                    '0',
                    $this->locale->formatPrice(0, $currency->iso_code),
                    0,
                    $this->locale->formatPrice(0, $currency->iso_code),
                    '0',
                    $pack_item['location'],
                    null,
                    '',
                    $packItemType,
                    (bool) Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock($pack_item['id_product']))
                );
            }

            $productsForViewing[] = new OrderProductForViewing(
                $product['id_order_detail'],
                $product['product_id'],
                $product['product_attribute_id'],
                $product['product_name'],
                $product['product_reference'],
                $product['product_supplier_reference'],
                $product['product_quantity'],
                $unitPriceFormatted,
                $totalPriceFormatted,
                $product['current_stock'],
                $imagePath,
                (new DecimalNumber((string) $product['unit_price_tax_excl']))->round($precision, $this->getNumberRoundMode()),
                (new DecimalNumber((string) $product['unit_price_tax_incl']))->round($precision, $this->getNumberRoundMode()),
                (string) $product['tax_rate'],
                $this->locale->formatPrice($product['amount_refunded'], $currency->iso_code),
                $product['product_quantity_refunded'] + $product['product_quantity_return'],
                $this->locale->formatPrice($product['displayed_max_refundable'], $currency->iso_code),
                (string) $product['displayed_max_refundable'],
                $product['location'],
                !empty($product['id_order_invoice']) ? $product['id_order_invoice'] : null,
                !empty($product['id_order_invoice'])
                    ? $orderInvoice->getInvoiceNumberFormatted((int) $order->getAssociatedLanguage()->getId())
                    : '',
                $productType,
                (bool) Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock($product['product_id'])),
                $packItems,
                $product['customizations']
            );
        }

        $offset = $query->getOffset();
        $limit = $query->getLimit();

        //@todo: its not really paginated, as all products are retrieved from legacy Order::getProducts(). But could be improved in future.
        if (null !== $offset && $limit) {
            $productsForViewing = array_slice($products, (int) $offset, (int) $limit);
        }

        return new OrderProductsForViewing($productsForViewing);
    }

    /**
     * @param array $pack_item
     */
    private function setProductImageInformation(&$pack_item): void
    {
        if (isset($pack_item['id_product_attribute']) && $pack_item['id_product_attribute']) {
            $id_image = Db::getInstance()->getValue('
                SELECT `image_shop`.id_image
                FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai' .
                Shop::addSqlAssociation('image', 'pai', true) . '
                WHERE id_product_attribute = ' . (int) $pack_item['id_product_attribute']);
        }

        if (!isset($id_image) || !$id_image) {
            $id_image = Db::getInstance()->getValue('
                SELECT `image_shop`.id_image
                FROM `' . _DB_PREFIX_ . 'image` i' .
                Shop::addSqlAssociation('image', 'i', true, 'image_shop.cover=1') . '
                WHERE i.id_product = ' . (int) $pack_item['id_product']
            );
        }

        $pack_item['image'] = $id_image ? new Image((int) $id_image) : null;
        $pack_item['image_size'] = null;
    }
}
