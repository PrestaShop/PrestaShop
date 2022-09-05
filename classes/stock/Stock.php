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

/**
 * Represents the products kept in warehouses.
 *
 * @since 1.5.0
 */
class StockCore extends ObjectModel
{
    /** @var int identifier of the warehouse */
    public $id_warehouse;

    /** @var int identifier of the product */
    public $id_product;

    /** @var int identifier of the product attribute if necessary */
    public $id_product_attribute;

    /** @var string Product reference */
    public $reference;

    /** @var string Product EAN13 */
    public $ean13;

    /** @var string Product ISBN */
    public $isbn;

    /** @var string UPC */
    public $upc;

    /** @var string MPN */
    public $mpn;

    /** @var int the physical quantity in stock for the current product in the current warehouse */
    public $physical_quantity;

    /** @var int the usable quantity (for sale) of the current physical quantity */
    public $usable_quantity;

    /** @var float the unit price without tax forthe current product */
    public $price_te;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'stock',
        'primary' => 'id_stock',
        'fields' => [
            'id_warehouse' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'reference' => ['type' => self::TYPE_STRING, 'validate' => 'isReference'],
            'ean13' => ['type' => self::TYPE_STRING, 'validate' => 'isEan13'],
            'isbn' => ['type' => self::TYPE_STRING, 'validate' => 'isIsbn'],
            'upc' => ['type' => self::TYPE_STRING, 'validate' => 'isUpc'],
            'mpn' => ['type' => self::TYPE_STRING, 'validate' => 'isMpn'],
            'physical_quantity' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'usable_quantity' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'price_te' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
        ],
    ];

    /**
     * @see ObjectModel::$webserviceParameters
     */
    protected $webserviceParameters = [
        'fields' => [
            'id_warehouse' => ['xlink_resource' => 'warehouses'],
            'id_product' => ['xlink_resource' => 'products'],
            'id_product_attribute' => ['xlink_resource' => 'combinations'],
            'real_quantity' => ['getter' => 'getWsRealQuantity', 'setter' => false],
        ],
        'hidden_fields' => [
        ],
    ];

    /**
     * @see ObjectModel::update()
     */
    public function update($null_values = false)
    {
        $this->getProductInformations();

        return parent::update($null_values);
    }

    /**
     * @see ObjectModel::add()
     */
    public function add($autodate = true, $null_values = false)
    {
        $this->getProductInformations();

        return parent::add($autodate, $null_values);
    }

    /**
     * Gets reference, ean13 , isbn, mpn and upc of the current product
     * Stores it in stock for stock_mvt integrity and history purposes.
     */
    protected function getProductInformations()
    {
        // if combinations
        if ((int) $this->id_product_attribute > 0) {
            $query = new DbQuery();
            $query->select('reference, ean13, isbn, mpn, upc');
            $query->from('product_attribute');
            $query->where('id_product = ' . (int) $this->id_product);
            $query->where('id_product_attribute = ' . (int) $this->id_product_attribute);
            $rows = Db::getInstance()->executeS($query);

            if (!is_array($rows)) {
                return;
            }

            foreach ($rows as $row) {
                $this->reference = $row['reference'];
                $this->ean13 = $row['ean13'];
                $this->isbn = $row['isbn'];
                $this->upc = $row['upc'];
                $this->mpn = $row['mpn'];
            }
        } else {
            // else, simple product

            $product = new Product((int) $this->id_product);
            if (Validate::isLoadedObject($product)) {
                $this->reference = $product->reference;
                $this->ean13 = $product->ean13;
                $this->isbn = $product->isbn;
                $this->upc = $product->upc;
                $this->mpn = $product->mpn;
            }
        }
    }

    /**
     * Webservice : used to get the real quantity of a product.
     */
    public function getWsRealQuantity()
    {
        $manager = StockManagerFactory::getManager();
        $quantity = $manager->getProductRealQuantities($this->id_product, $this->id_product_attribute, $this->id_warehouse, true);

        return $quantity;
    }

    public static function deleteStockByIds($id_product = null, $id_product_attribute = null)
    {
        if (!$id_product || !$id_product_attribute) {
            return false;
        }

        return Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'stock WHERE `id_product` = ' . (int) $id_product . ' AND `id_product_attribute` = ' . (int) $id_product_attribute);
    }

    public static function productIsPresentInStock($id_product = 0, $id_product_attribute = 0, $id_warehouse = 0)
    {
        if (!(int) $id_product && !is_int($id_product_attribute) && !(int) $id_warehouse) {
            return false;
        }

        $result = Db::getInstance()->executeS('SELECT `id_stock` FROM ' . _DB_PREFIX_ . 'stock
			WHERE `id_warehouse` = ' . (int) $id_warehouse . ' AND `id_product` = ' . (int) $id_product . ((int) $id_product_attribute ? ' AND `id_product_attribute` = ' . $id_product_attribute : ''));

        return is_array($result) && !empty($result) ? true : false;
    }
}
