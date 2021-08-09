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

use PrestaShopBundle\Translation\Translator;

/**
 * Class CombinationCore.
 */
class CombinationCore extends ObjectModel
{
    /** @var int Product ID */
    public $id_product;

    public $reference;

    /** @var string */
    public $supplier_reference;

    /**
     * @deprecated since 1.7.8
     * @see StockAvailable::$location instead
     *
     * @var string
     */
    public $location = '';

    public $ean13;

    public $isbn;

    public $upc;

    public $mpn;

    public $wholesale_price;

    public $price;

    public $unit_price_impact;

    public $ecotax;

    public $minimal_quantity = 1;

    /** @var int|null Low stock for mail alert */
    public $low_stock_threshold = null;

    /** @var bool Low stock mail alert activated */
    public $low_stock_alert = false;

    /**
     * @deprecated since 1.7.8
     * @see StockAvailable::$quantity instead
     *
     * @var int
     */
    public $quantity;

    public $weight;

    public $default_on;

    public $available_date = '0000-00-00';

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'product_attribute',
        'primary' => 'id_product_attribute',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'shop' => 'both', 'validate' => 'isUnsignedId', 'required' => true],
            'location' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'ean13' => ['type' => self::TYPE_STRING, 'validate' => 'isEan13', 'size' => 13],
            'isbn' => ['type' => self::TYPE_STRING, 'validate' => 'isIsbn', 'size' => 32],
            'upc' => ['type' => self::TYPE_STRING, 'validate' => 'isUpc', 'size' => 12],
            'mpn' => ['type' => self::TYPE_STRING, 'validate' => 'isMpn', 'size' => 40],
            'quantity' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 10],
            'reference' => ['type' => self::TYPE_STRING, 'size' => 64],
            'supplier_reference' => ['type' => self::TYPE_STRING, 'size' => 64],

            /* Shop fields */
            'wholesale_price' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isNegativePrice', 'size' => 27],
            'price' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isNegativePrice', 'size' => 20],
            'ecotax' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'size' => 20],
            'weight' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isFloat'],
            'unit_price_impact' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isNegativePrice', 'size' => 20],
            'minimal_quantity' => ['type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId', 'required' => true],
            'low_stock_threshold' => ['type' => self::TYPE_INT, 'shop' => true, 'allow_null' => true, 'validate' => 'isInt'],
            'low_stock_alert' => ['type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'],
            'default_on' => ['type' => self::TYPE_BOOL, 'allow_null' => true, 'shop' => true, 'validate' => 'isBool'],
            'available_date' => ['type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDateFormat'],
        ],
    ];

    protected $webserviceParameters = [
        'objectNodeName' => 'combination',
        'objectsNodeName' => 'combinations',
        'fields' => [
            'id_product' => ['required' => true, 'xlink_resource' => 'products'],
        ],
        'associations' => [
            'product_option_values' => ['resource' => 'product_option_value'],
            'images' => ['resource' => 'image', 'api' => 'images/products'],
        ],
    ];

    /**
     * @param int|null $id
     * @param int|null $id_lang
     * @param int|null $id_shop
     * @param Translator|null $translator
     */
    public function __construct(?int $id = null, ?int $id_lang = null, ?int $id_shop = null, ?Translator $translator = null)
    {
        parent::__construct($id, $id_lang, $id_shop, $translator);
        $this->loadStockData();
    }

    /**
     * Fill the variables used for stock management.
     */
    public function loadStockData(): void
    {
        if (false === Validate::isLoadedObject($this)) {
            return;
        }

        $this->quantity = StockAvailable::getQuantityAvailableByProduct($this->id_product, $this->id);
        $this->location = StockAvailable::getLocation($this->id_product, $this->id);
    }

    /**
     * Deletes current Combination from the database.
     *
     * @return bool True if delete was successful
     *
     * @throws PrestaShopException
     */
    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }

        // Removes the product from StockAvailable, for the current shop
        StockAvailable::removeProductFromStockAvailable((int) $this->id_product, (int) $this->id);

        if ($specificPrices = SpecificPrice::getByProductId((int) $this->id_product, (int) $this->id)) {
            foreach ($specificPrices as $specificPrice) {
                $price = new SpecificPrice((int) $specificPrice['id_specific_price']);
                $price->delete();
            }
        }

        if (!$this->hasMultishopEntries() && !$this->deleteAssociations()) {
            return false;
        }

        if (!$this->deleteCartProductCombination()) {
            return false;
        }

        $this->deleteFromSupplier($this->id_product);
        Product::updateDefaultAttribute($this->id_product);
        Tools::clearColorListCache((int) $this->id_product);

        return true;
    }

    /**
     * Delete from Supplier.
     *
     * @param int $idProduct Product ID
     *
     * @return bool
     */
    public function deleteFromSupplier($idProduct)
    {
        return Db::getInstance()->delete('product_supplier', 'id_product = ' . (int) $idProduct
            . ' AND id_product_attribute = ' . (int) $this->id);
    }

    /**
     * Adds current Combination as a new Object to the database.
     *
     * @param bool $autoDate Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Combination has been successfully added
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = false)
    {
        if ($this->default_on) {
            $this->default_on = 1;
        } else {
            $this->default_on = null;
        }

        if (!parent::add($autoDate, $nullValues)) {
            return false;
        }

        $product = new Product((int) $this->id_product);
        if ($product->getType() == Product::PTYPE_VIRTUAL) {
            StockAvailable::setProductOutOfStock((int) $this->id_product, 1, null, (int) $this->id);
        } else {
            StockAvailable::setProductOutOfStock((int) $this->id_product, StockAvailable::outOfStock((int) $this->id_product), null, $this->id);
        }

        SpecificPriceRule::applyAllRules([(int) $this->id_product]);

        Product::updateDefaultAttribute($this->id_product);

        return true;
    }

    /**
     * Updates the current Combination in the database.
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Combination has been successfully updated
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        if ($this->default_on) {
            $this->default_on = 1;
        } else {
            $this->default_on = null;
        }

        $return = parent::update($nullValues);
        Product::updateDefaultAttribute($this->id_product);

        return $return;
    }

    /**
     * Delete associations.
     *
     * @return bool Indicates whether associations have been successfully deleted
     */
    public function deleteAssociations()
    {
        if ((int) $this->id === 0) {
            return false;
        }
        $result = Db::getInstance()->delete('product_attribute_combination', '`id_product_attribute` = ' . (int) $this->id);
        $result &= Db::getInstance()->delete('product_attribute_image', '`id_product_attribute` = ' . (int) $this->id);

        if ($result) {
            Hook::exec('actionAttributeCombinationDelete', ['id_product_attribute' => (int) $this->id]);
        }

        return $result;
    }

    /**
     * Delete product combination from cart.
     *
     * @return bool
     */
    protected function deleteCartProductCombination(): bool
    {
        if ((int) $this->id === 0) {
            return false;
        }

        return Db::getInstance()->delete('cart_product', 'id_product_attribute = ' . (int) $this->id);
    }

    /**
     * @param array $idsAttribute
     *
     * @return bool
     */
    public function setAttributes($idsAttribute)
    {
        $result = $this->deleteAssociations();
        if ($result && !empty($idsAttribute)) {
            $sqlValues = [];
            foreach ($idsAttribute as $value) {
                $sqlValues[] = '(' . (int) $value . ', ' . (int) $this->id . ')';
            }

            $result = Db::getInstance()->execute(
                '
				INSERT INTO `' . _DB_PREFIX_ . 'product_attribute_combination` (`id_attribute`, `id_product_attribute`)
				VALUES ' . implode(',', $sqlValues)
            );
            if ($result) {
                Hook::exec('actionAttributeCombinationSave', ['id_product_attribute' => (int) $this->id, 'id_attributes' => $idsAttribute]);
            }
        }

        return $result;
    }

    /**
     * @param array $values
     *
     * @return bool
     */
    public function setWsProductOptionValues($values)
    {
        $idsAttributes = [];
        foreach ($values as $value) {
            $idsAttributes[] = $value['id'];
        }

        return $this->setAttributes($idsAttributes);
    }

    /**
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public function getWsProductOptionValues()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT a.id_attribute AS id
			FROM `' . _DB_PREFIX_ . 'product_attribute_combination` a
			' . Shop::addSqlAssociation('attribute', 'a') . '
			WHERE a.id_product_attribute = ' . (int) $this->id);

        return $result;
    }

    /**
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public function getWsImages()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT a.`id_image` as id
			FROM `' . _DB_PREFIX_ . 'product_attribute_image` a
			' . Shop::addSqlAssociation('product_attribute', 'a') . '
			WHERE a.`id_product_attribute` = ' . (int) $this->id . '
		');
    }

    /**
     * @param $idsImage
     *
     * @return bool
     */
    public function setImages($idsImage)
    {
        if (Db::getInstance()->execute('
			DELETE FROM `' . _DB_PREFIX_ . 'product_attribute_image`
			WHERE `id_product_attribute` = ' . (int) $this->id) === false) {
            return false;
        }

        if (is_array($idsImage) && count($idsImage)) {
            $sqlValues = [];

            foreach ($idsImage as $value) {
                $sqlValues[] = '(' . (int) $this->id . ', ' . (int) $value . ')';
            }

            if (is_array($sqlValues) && count($sqlValues)) {
                Db::getInstance()->execute(
                    '
					INSERT INTO `' . _DB_PREFIX_ . 'product_attribute_image` (`id_product_attribute`, `id_image`)
					VALUES ' . implode(',', $sqlValues)
                );
            }
        }

        return true;
    }

    /**
     * @param $values
     *
     * @return bool
     */
    public function setWsImages($values)
    {
        $idsImages = [];
        foreach ($values as $value) {
            $idsImages[] = (int) $value['id'];
        }

        return $this->setImages($idsImages);
    }

    /**
     * @param $idLang
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public function getAttributesName($idLang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT al.*
			FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac
			JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON (pac.id_attribute = al.id_attribute AND al.id_lang=' . (int) $idLang . ')
			WHERE pac.id_product_attribute=' . (int) $this->id);
    }

    /**
     * This method is allow to know if a feature is active.
     *
     * @since 1.5.0.1
     *
     * @return bool
     */
    public static function isFeatureActive()
    {
        static $feature_active = null;

        if ($feature_active === null) {
            $feature_active = (bool) Configuration::get('PS_COMBINATION_FEATURE_ACTIVE');
        }

        return $feature_active;
    }

    /**
     * This method is allow to know if a Combination entity is currently used.
     *
     * @since 1.5.0.1
     *
     * @param $table
     * @param $hasActiveColumn
     *
     * @return bool
     */
    public static function isCurrentlyUsed($table = null, $hasActiveColumn = false)
    {
        return parent::isCurrentlyUsed('product_attribute');
    }

    /**
     * For a given ean13 reference, returns the corresponding id.
     *
     * @param string $ean13
     *
     * @return int|string Product attribute identifier
     */
    public static function getIdByEan13($ean13)
    {
        if (empty($ean13)) {
            return 0;
        }

        if (!Validate::isEan13($ean13)) {
            return 0;
        }

        $query = new DbQuery();
        $query->select('pa.id_product_attribute');
        $query->from('product_attribute', 'pa');
        $query->where('pa.ean13 = \'' . pSQL($ean13) . '\'');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * For a given product_attribute reference, returns the corresponding id.
     *
     * @param int $idProduct
     * @param string $reference
     *
     * @return int id
     */
    public static function getIdByReference($idProduct, $reference)
    {
        if (empty($reference)) {
            return 0;
        }

        $query = new DbQuery();
        $query->select('pa.id_product_attribute');
        $query->from('product_attribute', 'pa');
        $query->where('pa.reference LIKE \'%' . pSQL($reference) . '%\'');
        $query->where('pa.id_product = ' . (int) $idProduct);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public function getColorsAttributes()
    {
        return Db::getInstance()->executeS('
			SELECT a.id_attribute
			FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac
			JOIN ' . _DB_PREFIX_ . 'attribute a ON (pac.id_attribute = a.id_attribute)
			JOIN ' . _DB_PREFIX_ . 'attribute_group ag ON (ag.id_attribute_group = a.id_attribute_group)
			WHERE pac.id_product_attribute=' . (int) $this->id . ' AND ag.is_color_group = 1
		');
    }

    /**
     * Retrive the price of combination.
     *
     * @param int $idProductAttribute
     *
     * @return float mixed
     *
     * @since 1.5.0
     */
    public static function getPrice($idProductAttribute)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            '
			SELECT product_attribute_shop.`price`
			FROM `' . _DB_PREFIX_ . 'product_attribute` pa
			' . Shop::addSqlAssociation('product_attribute', 'pa') . '
			WHERE pa.`id_product_attribute` = ' . (int) $idProductAttribute
        );
    }
}
