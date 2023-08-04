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

use PrestaShop\PrestaShop\Core\Domain\Combination\CombinationSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;

/**
 * Class CombinationCore.
 */
class CombinationCore extends ObjectModel
{
    /** @var int Product ID */
    public $id_product;

    public $reference;

    /**
     * @deprecated since 8.1.0
     *
     * @var string
     */
    public $supplier_reference;

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

    public $weight;

    /** @var bool|null */
    public $default_on;

    public $available_date = '0000-00-00';

    /** @var string|array Text when in stock or array of text by id_lang */
    public $available_now;

    /** @var string|array Text when not in stock but available to order or array of text by id_lang */
    public $available_later;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'product_attribute',
        'primary' => 'id_product_attribute',
        'multilang' => true,
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'shop' => 'both', 'validate' => 'isUnsignedId', 'required' => true],
            'ean13' => ['type' => self::TYPE_STRING, 'validate' => 'isEan13', 'size' => 13],
            'isbn' => ['type' => self::TYPE_STRING, 'validate' => 'isIsbn', 'size' => 32],
            'upc' => ['type' => self::TYPE_STRING, 'validate' => 'isUpc', 'size' => 12],
            'mpn' => ['type' => self::TYPE_STRING, 'validate' => 'isMpn', 'size' => 40],
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

            /* Lang fields */
            'available_now' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => CombinationSettings::MAX_AVAILABLE_NOW_LABEL_LENGTH],
            'available_later' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'IsGenericName', 'size' => CombinationSettings::MAX_AVAILABLE_LATER_LABEL_LENGTH],
        ],
    ];

    protected $webserviceParameters = [
        'objectNodeName' => 'combination',
        'objectsNodeName' => 'combinations',
        'fields' => [
            'id_product' => ['required' => true, 'xlink_resource' => 'products'],
        ],
        'associations' => [
            'product_option_values' => [
                'resource' => 'product_option_value',
                'fields' => [
                    'id' => ['required' => true],
                ],
            ],
            'images' => [
                'resource' => 'image',
                'fields' => ['id' => []],
            ],
        ],
    ];

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

        $shopIdsList = $this->getShopIdsList();

        // Removes the product from StockAvailable for the related shops
        if (!empty($shopIdsList)) {
            foreach ($shopIdsList as $shopId) {
                StockAvailable::removeProductFromStockAvailable((int) $this->id_product, (int) $this->id, $shopId);
            }
        } else {
            StockAvailable::removeProductFromStockAvailable((int) $this->id_product, (int) $this->id);
        }

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
        $this->deleteFromPack();
        Product::updateDefaultAttribute($this->id_product);

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
        if ($this->hasMultishopEntries()) {
            return true;
        }

        return Db::getInstance()->delete('product_supplier', 'id_product = ' . (int) $idProduct
            . ' AND id_product_attribute = ' . (int) $this->id);
    }

    /**
     * Delete association with Pack.
     *
     * @return bool
     */
    protected function deleteFromPack(): bool
    {
        if ($this->hasMultishopEntries()) {
            return true;
        }

        return Db::getInstance()->delete('pack', 'id_product_item = ' . (int) $this->id_product
            . ' AND id_product_attribute_item = ' . (int) $this->id);
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
            $this->default_on = true;
        } else {
            $this->default_on = null;
        }

        if (!parent::add($autoDate, $nullValues)) {
            return false;
        }

        $product = new Product((int) $this->id_product);
        $shopIdsList = $this->getShopIdsList();

        if ($product->getType() == Product::PTYPE_VIRTUAL) {
            $outOfStock = OutOfStockType::OUT_OF_STOCK_AVAILABLE;
        } else {
            $outOfStock = StockAvailable::outOfStock((int) $this->id_product);
        }

        if (!empty($shopIdsList)) {
            foreach ($shopIdsList as $shopId) {
                StockAvailable::setProductOutOfStock((int) $this->id_product, $outOfStock, $shopId, (int) $this->id);
            }
        } else {
            // This creates stock_available for combination as a side effect
            StockAvailable::setProductOutOfStock((int) $this->id_product, $outOfStock, $this->id_shop, $this->id);
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
            $this->default_on = true;
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
        $result = Db::getInstance()->delete(
            'product_attribute_combination',
            '`id_product_attribute` = ' . (int) $this->id
        );
        $result = $result && Db::getInstance()->delete(
            'product_attribute_image',
            '`id_product_attribute` = ' . (int) $this->id
        );

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

        if ($this->hasMultishopEntries()) {
            $shopIdList = $this->getShopIdsList();

            return Db::getInstance()->delete('cart_product', 'id_product_attribute = ' . (int) $this->id . ' AND id_shop IN (' . implode(',', $shopIdList) . ')');
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
     * @param array<int> $idsImage
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

            Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'product_attribute_image` (`id_product_attribute`, `id_image`)
					VALUES ' . implode(',', $sqlValues)
            );
        }

        return true;
    }

    /**
     * @param array<array{id: int}> $values
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
     * @param int $idLang
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
     * @param string|null $table Name of table linked to entity
     * @param bool $hasActiveColumn True if the table has an active column
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
     * @return int ID
     */
    public static function getIdByReference($idProduct, $reference)
    {
        if (empty($reference)) {
            return 0;
        }

        $query = new DbQuery();
        $query->select('pa.id_product_attribute');
        $query->from('product_attribute', 'pa');
        $query->where('pa.reference = \'' . pSQL($reference) . '\'');
        $query->where('pa.id_product = ' . (int) $idProduct);

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
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
     * @return string
     *
     * @since 1.5.0
     */
    public static function getPrice($idProductAttribute)
    {
        return (string) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT product_attribute_shop.`price`
			FROM `' . _DB_PREFIX_ . 'product_attribute` pa
			' . Shop::addSqlAssociation('product_attribute', 'pa') . '
			WHERE pa.`id_product_attribute` = ' . (int) $idProductAttribute
        );
    }
}
