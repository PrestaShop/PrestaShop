<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class CombinationCore
 */
class CombinationCore extends ObjectModel
{
    /** @var int $id_product Product ID */
    public $id_product;

    public $reference;

    /** @var string $supplier_reference */
    public $supplier_reference;

    public $location;

    public $ean13;

    public $isbn;

    public $upc;

    public $wholesale_price;

    public $price;

    public $unit_price_impact;

    public $ecotax;

    public $minimal_quantity = 1;

    public $quantity;

    public $weight;

    public $default_on;

    public $available_date = '0000-00-00';

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'product_attribute',
        'primary' => 'id_product_attribute',
        'fields' => array(
            'id_product' =>        array('type' => self::TYPE_INT, 'shop' => 'both', 'validate' => 'isUnsignedId', 'required' => true),
            'location' =>            array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64),
            'ean13' =>                array('type' => self::TYPE_STRING, 'validate' => 'isEan13', 'size' => 13),
            'isbn' =>                array('type' => self::TYPE_STRING, 'validate' => 'isIsbn', 'size' => 13),
            'upc' =>                array('type' => self::TYPE_STRING, 'validate' => 'isUpc', 'size' => 12),
            'quantity' =>            array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 10),
            'reference' =>            array('type' => self::TYPE_STRING, 'size' => 32),
            'supplier_reference' => array('type' => self::TYPE_STRING, 'size' => 32),

            /* Shop fields */
            'wholesale_price' =>    array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'size' => 27),
            'price' =>                array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isNegativePrice', 'size' => 20),
            'ecotax' =>            array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'size' => 20),
            'weight' =>            array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isFloat'),
            'unit_price_impact' =>    array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isNegativePrice', 'size' => 20),
            'minimal_quantity' =>    array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId', 'required' => true),
            'default_on' =>        array('type' => self::TYPE_BOOL, 'allow_null' => true, 'shop' => true, 'validate' => 'isBool'),
            'available_date' =>    array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDateFormat'),
        ),
    );

    protected $webserviceParameters = array(
        'objectNodeName' => 'combination',
        'objectsNodeName' => 'combinations',
        'fields' => array(
            'id_product' => array('required' => true, 'xlink_resource'=> 'products'),
        ),
        'associations' => array(
            'product_option_values' => array('resource' => 'product_option_value'),
            'images' => array('resource' => 'image', 'api' => 'images/products'),
        ),
    );

    /**
     * Deletes current Combination from the database
     *
     * @return bool True if delete was successful
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

        $this->deleteFromSupplier($this->id_product);
        Product::updateDefaultAttribute($this->id_product);
        Tools::clearColorListCache((int) $this->id_product);

        return true;
    }

    /**
     * Delete from Supplier
     *
     * @param int $idProduct Product ID
     *
     * @return bool
     */
    public function deleteFromSupplier($idProduct)
    {
        return Db::getInstance()->delete('product_supplier', 'id_product = '.(int) $idProduct
            .' AND id_product_attribute = '.(int) $this->id);
    }

    /**
     * Adds current Combination as a new Object to the database
     *
     * @param bool $autoDate   Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Combination has been successfully added
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

        SpecificPriceRule::applyAllRules(array((int) $this->id_product));

        Product::updateDefaultAttribute($this->id_product);

        return true;
    }

    /**
     * Updates the current Combination in the database
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Combination has been successfully updated
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
     * Delete associations
     *
     * @return bool Indicates whether associations have been successfully deleted
     */
    public function deleteAssociations()
    {
        $result = Db::getInstance()->delete('product_attribute_combination', '`id_product_attribute` = '.(int) $this->id);
        $result &= Db::getInstance()->delete('cart_product', '`id_product_attribute` = '.(int) $this->id);
        $result &= Db::getInstance()->delete('product_attribute_image', '`id_product_attribute` = '.(int) $this->id);

        if ($result) {
            Hook::exec('actionAttributeCombinationDelete', array('id_product_attribute' => (int)$this->id));
        }

        return $result;
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
            $sqlValues = array();
            foreach ($idsAttribute as $value) {
                $sqlValues[] = '('.(int) $value.', '.(int) $this->id.')';
            }

            $result = Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'product_attribute_combination` (`id_attribute`, `id_product_attribute`)
				VALUES '.implode(',', $sqlValues)
            );
            if ($result) {
                Hook::exec('actionAttributeCombinationSave', array('id_product_attribute' => (int)$this->id, 'id_attributes' => $idsAttribute));
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
        $idsAttributes = array();
        foreach ($values as $value) {
            $idsAttributes[] = $value['id'];
        }
        return $this->setAttributes($idsAttributes);
    }

    /**
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getWsProductOptionValues()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT a.id_attribute AS id
			FROM `'._DB_PREFIX_.'product_attribute_combination` a
			'.Shop::addSqlAssociation('attribute', 'a').'
			WHERE a.id_product_attribute = '.(int) $this->id);

        return $result;
    }

    /**
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getWsImages()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT a.`id_image` as id
			FROM `'._DB_PREFIX_.'product_attribute_image` a
			'.Shop::addSqlAssociation('product_attribute', 'a').'
			WHERE a.`id_product_attribute` = '.(int) $this->id.'
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
			DELETE FROM `'._DB_PREFIX_.'product_attribute_image`
			WHERE `id_product_attribute` = '.(int) $this->id) === false) {
            return false;
        }

        if (is_array($idsImage) && count($idsImage)) {
            $sqlValues = array();

            foreach ($idsImage as $value) {
                $sqlValues[] = '('.(int) $this->id.', '.(int) $value.')';
            }

            if (is_array($sqlValues) && count($sqlValues)) {
                Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'product_attribute_image` (`id_product_attribute`, `id_image`)
					VALUES '.implode(',', $sqlValues)
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
        $idsImages = array();
        foreach ($values as $value) {
            $idsImages[] = (int) $value['id'];
        }
        return $this->setImages($idsImages);
    }

    /**
     * @param $idLang
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getAttributesName($idLang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT al.*
			FROM '._DB_PREFIX_.'product_attribute_combination pac
			JOIN '._DB_PREFIX_.'attribute_lang al ON (pac.id_attribute = al.id_attribute AND al.id_lang='.(int) $idLang.')
			WHERE pac.id_product_attribute='.(int) $this->id);
    }

    /**
     * This method is allow to know if a feature is active
     * @since 1.5.0.1
     * @return bool
     */
    public static function isFeatureActive()
    {
        static $feature_active = null;

        if ($feature_active === null) {
            $feature_active = Configuration::get('PS_COMBINATION_FEATURE_ACTIVE');
        }
        return $feature_active;
    }

    /**
     * This method is allow to know if a Combination entity is currently used
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
     * For a given product_attribute reference, returns the corresponding id
     *
     * @param int    $idProduct
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
        $query->where('pa.reference LIKE \'%'.pSQL($reference).'%\'');
        $query->where('pa.id_product = '.(int) $idProduct);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getColorsAttributes()
    {
        return Db::getInstance()->executeS('
			SELECT a.id_attribute
			FROM '._DB_PREFIX_.'product_attribute_combination pac
			JOIN '._DB_PREFIX_.'attribute a ON (pac.id_attribute = a.id_attribute)
			JOIN '._DB_PREFIX_.'attribute_group ag ON (ag.id_attribute_group = a.id_attribute_group)
			WHERE pac.id_product_attribute='.(int)$this->id.' AND ag.is_color_group = 1
		');
    }

    /**
     * Retrive the price of combination
     *
     * @param int $idProductAttribute
     *
     * @return float mixed
     *
     * @since 1.5.0
     */
    public static function getPrice($idProductAttribute)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT product_attribute_shop.`price`
			FROM `'._DB_PREFIX_.'product_attribute` pa
			'.Shop::addSqlAssociation('product_attribute', 'pa').'
			WHERE pa.`id_product_attribute` = '.(int) $idProductAttribute
        );
    }
}
