<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class CustomizationCore.
 */
class CustomizationCore extends ObjectModel
{
    /** @var int */
    public $id_product_attribute;

    /** @var int */
    public $id_address_delivery;

    /** @var int */
    public $id_cart;

    /** @var int */
    public $id_product;

    /** @var int */
    public $quantity;

    /** @var int */
    public $quantity_refunded;

    /** @var int */
    public $quantity_returned;

    /** @var bool */
    public $in_cart;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'customization',
        'primary' => 'id_customization',
        'fields' => array(
            /* Classic fields */
            'id_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_address_delivery' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'quantity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'quantity_refunded' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'quantity_returned' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'in_cart' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
        ),
    );

    protected $webserviceParameters = array(
        'fields' => array(
            'id_address_delivery' => array(
                'xlink_resource' => array(
                    'resourceName' => 'addresses',
                ),
            ),
            'id_cart' => array(
                'xlink_resource' => array(
                    'resourceName' => 'carts',
                ),
            ),
            'id_product' => array(
                'xlink_resource' => array(
                    'resourceName' => 'products',
                ),
            ),
        ),
        'associations' => array(
            'customized_data_text_fields' => array(
                'resource' => 'customized_data_text_field',
                'virtual_entity' => true,
                'fields' => array(
                    'id_customization_field' => array('required' => true, 'xlink_resource' => 'product_customization_fields'),
                    'value' => array(),
                ),
            ),
            'customized_data_images' => array(
                'resource' => 'customized_data_image',
                'virtual_entity' => true,
                'setter' => false,
                'fields' => array(
                    'id_customization_field' => array('xlink_resource' => 'product_customization_fields'),
                    'value' => array(),
                ),
            ),
        ),
    );

    /**
     * Get returned Customizations.
     *
     * @param int $idOrder Order ID
     *
     * @return array|bool
     */
    public static function getReturnedCustomizations($idOrder)
    {
        if (($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT ore.`id_order_return`, ord.`id_order_detail`, ord.`id_customization`, ord.`product_quantity`
			FROM `' . _DB_PREFIX_ . 'order_return` ore
			INNER JOIN `' . _DB_PREFIX_ . 'order_return_detail` ord ON (ord.`id_order_return` = ore.`id_order_return`)
			WHERE ore.`id_order` = ' . (int) ($idOrder) . ' AND ord.`id_customization` != 0')) === false) {
            return false;
        }
        $customizations = array();
        foreach ($result as $row) {
            $customizations[(int) ($row['id_customization'])] = $row;
        }

        return $customizations;
    }

    /**
     * Get ordered Customizations.
     *
     * @param int $idCart Cart ID
     *
     * @return array|bool Ordered Customizations
     *                    `false` if not found
     */
    public static function getOrderedCustomizations($idCart)
    {
        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_customization`, `quantity` FROM `' . _DB_PREFIX_ . 'customization` WHERE `id_cart` = ' . (int) ($idCart))) {
            return false;
        }
        $customizations = array();
        foreach ($result as $row) {
            $customizations[(int) ($row['id_customization'])] = $row;
        }

        return $customizations;
    }

    /**
     * Get price of Customization.
     *
     * @param int $idCustomization Customization ID
     *
     * @return float|int Price of customization
     */
    public static function getCustomizationPrice($idCustomization)
    {
        if (!(int) $idCustomization) {
            return 0;
        }

        return (float) Db::getInstance()->getValue(
            '
            SELECT SUM(`price`) FROM `' . _DB_PREFIX_ . 'customized_data`
            WHERE `id_customization` = ' . (int) $idCustomization
        );
    }

    /**
     * Get weight of Customization.
     *
     * @param int $idCustomization Customization ID
     *
     * @return float|int Weight
     */
    public static function getCustomizationWeight($idCustomization)
    {
        if (!(int) $idCustomization) {
            return 0;
        }

        return (float) Db::getInstance()->getValue(
            '
            SELECT SUM(`weight`) FROM `' . _DB_PREFIX_ . 'customized_data`
            WHERE `id_customization` = ' . (int) $idCustomization
        );
    }

    /**
     * Count Customization quantity by Product.
     *
     * @param array $customizations Customizations
     *
     * @return array Customization quantities by Product
     */
    public static function countCustomizationQuantityByProduct($customizations)
    {
        $total = array();
        foreach ($customizations as $customization) {
            $total[(int) $customization['id_order_detail']] = !isset($total[(int) $customization['id_order_detail']]) ? (int) $customization['quantity'] : $total[(int) $customization['id_order_detail']] + (int) $customization['quantity'];
        }

        return $total;
    }

    /**
     * Get label.
     *
     * @param int $idCustomization Customization ID
     * @param int $idLang Language IOD
     * @param int|null $idShop Shop ID
     *
     * @return bool|false|string|null
     */
    public static function getLabel($idCustomization, $idLang, $idShop = null)
    {
        if (!(int) $idCustomization || !(int) $idLang) {
            return false;
        }
        if (Shop::isFeatureActive() && !(int) $idShop) {
            $idShop = (int) Context::getContext()->shop->id;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            '
		SELECT `name`
		FROM `' . _DB_PREFIX_ . 'customization_field_lang`
		WHERE `id_customization_field` = ' . (int) $idCustomization . ((int) $idShop ? ' AND `id_shop` = ' . (int) $idShop : '') . '
		AND `id_lang` = ' . (int) $idLang
        );

        return $result;
    }

    /**
     * Retrieve quantities from IDs.
     *
     * @param array $idsCustomizations Customization IDs
     *
     * @return array Quantities
     */
    public static function retrieveQuantitiesFromIds($idsCustomizations)
    {
        $quantities = array();

        $inValues = '';
        foreach ($idsCustomizations as $key => $idCustomization) {
            if ($key > 0) {
                $inValues .= ',';
            }
            $inValues .= (int) $idCustomization;
        }

        if (!empty($inValues)) {
            $results = Db::getInstance()->executeS(
                            'SELECT `id_customization`, `id_product`, `quantity`, `quantity_refunded`, `quantity_returned`
							 FROM `' . _DB_PREFIX_ . 'customization`
							 WHERE `id_customization` IN (' . $inValues . ')'
            );

            foreach ($results as $row) {
                $quantities[$row['id_customization']] = $row;
            }
        }

        return $quantities;
    }

    /**
     * Count quantity by Cart.
     *
     * @param int $idCart Cart ID
     *
     * @return array
     */
    public static function countQuantityByCart($idCart)
    {
        $quantity = array();

        $results = Db::getInstance()->executeS('
			SELECT `id_product`, `id_product_attribute`, SUM(`quantity`) AS quantity
			FROM `' . _DB_PREFIX_ . 'customization`
			WHERE `id_cart` = ' . (int) $idCart . '
			GROUP BY `id_cart`, `id_product`, `id_product_attribute`
		');

        foreach ($results as $row) {
            $quantity[$row['id_product']][$row['id_product_attribute']] = $row['quantity'];
        }

        return $quantity;
    }

    /**
     * This method is allow to know if a feature is used or active.
     *
     * @since 1.5.0.1
     *
     * @return bool
     */
    public static function isFeatureActive()
    {
        return Configuration::get('PS_CUSTOMIZATION_FEATURE_ACTIVE');
    }

    /**
     * This method is allow to know if a Customization entity is currently used.
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
        return (bool) Db::getInstance()->getValue('
			SELECT `id_customization_field`
			FROM `' . _DB_PREFIX_ . 'customization_field`
		');
    }

    /**
     * Get customized text fields
     * (for webservice).
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public function getWsCustomizedDataTextFields()
    {
        if (!$results = Db::getInstance()->executeS('
			SELECT id_customization_field, value
			FROM `' . _DB_PREFIX_ . 'customization_field` cf
			LEFT JOIN `' . _DB_PREFIX_ . 'customized_data` cd ON (cf.id_customization_field = cd.index)
			WHERE `id_product` = ' . (int) $this->id_product . '
			AND id_customization = ' . (int) $this->id . '
			AND cf.type = ' . (int) Product::CUSTOMIZE_TEXTFIELD)) {
            return array();
        }

        return $results;
    }

    /**
     * Get customized images data
     * (for webservice).
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public function getWsCustomizedDataImages()
    {
        if (!$results = Db::getInstance()->executeS('
			SELECT id_customization_field, value
			FROM `' . _DB_PREFIX_ . 'customization_field` cf
			LEFT JOIN `' . _DB_PREFIX_ . 'customized_data` cd ON (cf.id_customization_field = cd.index)
			WHERE `id_product` = ' . (int) $this->id_product . '
			AND id_customization = ' . (int) $this->id . '
			AND cf.type = ' . (int) Product::CUSTOMIZE_FILE)) {
            return array();
        }

        return $results;
    }

    /**
     * Set customized text fields
     * (for webservice).
     *
     * @param array $values
     *
     * @return bool
     */
    public function setWsCustomizedDataTextFields($values)
    {
        $cart = new Cart($this->id_cart);
        if (!Validate::isLoadedObject($cart)) {
            WebserviceRequest::getInstance()->setError(500, $this->trans('Could not load cart id=%s', array($this->id_cart), 'Admin.Notifications.Error'), 137);

            return false;
        }
        Db::getInstance()->execute('
		DELETE FROM `' . _DB_PREFIX_ . 'customized_data`
		WHERE id_customization = ' . (int) $this->id . '
		AND type = ' . (int) Product::CUSTOMIZE_TEXTFIELD);
        foreach ($values as $value) {
            $query = 'INSERT INTO `' . _DB_PREFIX_ . 'customized_data` (`id_customization`, `type`, `index`, `value`)
				VALUES (' . (int) $this->id . ', ' . (int) Product::CUSTOMIZE_TEXTFIELD . ', ' . (int) $value['id_customization_field'] . ', \'' . pSQL($value['value']) . '\')';

            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Delete the current context shops langs.
     *
     * @param int $idCustomizationField
     * @param int[] $shopList
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     */
    public static function deleteCustomizationFieldLangByShop($idCustomizationField, $shopList)
    {
        $return = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'customization_field_lang` 
                WHERE `id_customization_field` = ' . (int) $idCustomizationField . ' 
                AND `id_shop` IN (' . implode(',', $shopList) . ')');

        if (!$return) {
            throw new PrestaShopDatabaseException('An error occurred while deletion the customization fields lang');
        }

        return $return;
    }
}
