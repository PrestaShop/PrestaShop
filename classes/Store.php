<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class StoreCore
 */
class StoreCore extends ObjectModel
{
    /** @var int Store id */
    public $id;

    /** @var int Country id */
    public $id_country;

    /** @var int State id */
    public $id_state;

    /** @var string Store name */
    public $name;

    /** @var string Address first line */
    public $address1;

    /** @var string Address second line (optional) */
    public $address2;

    /** @var string Postal code */
    public $postcode;

    /** @var string City */
    public $city;

    /** @var float Latitude */
    public $latitude;

    /** @var float Longitude */
    public $longitude;

    /** @var string Store hours (PHP serialized) */
    public $hours;

    /** @var string Phone number */
    public $phone;

    /** @var string Fax number */
    public $fax;

    /** @var string Note */
    public $note;

    /** @var string e-mail */
    public $email;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /** @var bool Store status */
    public $active = true;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'store',
        'primary' => 'id_store',
        'multilang' => true,
        'fields' => array(
            'id_country' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_state' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'),
            'postcode' => array('type' => self::TYPE_STRING, 'size' => 12),
            'city' => array('type' => self::TYPE_STRING, 'validate' => 'isCityName', 'required' => true, 'size' => 64),
            'latitude' => array('type' => self::TYPE_FLOAT, 'validate' => 'isCoordinate', 'size' => 13),
            'longitude' => array('type' => self::TYPE_FLOAT, 'validate' => 'isCoordinate', 'size' => 13),
            'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 16),
            'fax' => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 16),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 128),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
            'address1' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isAddress', 'required' => true, 'size' => 128),
            'address2' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isAddress', 'size' => 128),
            'hours' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isJson', 'size' => 65000),
            'note' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 65000),
        ),
    );

    protected $webserviceParameters = array(
        'fields' => array(
            'id_country' => array('xlink_resource' => 'countries'),
            'id_state' => array('xlink_resource' => 'states'),
            'hours' => array('getter' => 'getWsHours', 'setter' => 'setWsHours'),
        ),
    );

    /**
     * StoreCore constructor.
     *
     * @param null $idStore
     * @param null $idLang
     */
    public function __construct($idStore = null, $idLang = null)
    {
        parent::__construct($idStore);
        $this->id_image = ($this->id && file_exists(_PS_STORE_IMG_DIR_.(int) $this->id.'.jpg')) ? (int) $this->id : false;
        $this->image_dir = _PS_STORE_IMG_DIR_;
    }

    /**
     * Get Stores by language
     *
     * @param $idLang
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getStores($idLang)
    {
        $stores = Db::getInstance()->executeS('
            SELECT s.id_store AS `id`, s.*, sl.*
            FROM '._DB_PREFIX_.'store s
            '.Shop::addSqlAssociation('store', 's').'
            LEFT JOIN ' . _DB_PREFIX_ . 'store_lang sl ON (
            sl.id_store = s.id_store
            AND sl.id_lang = ' . (int)$idLang . '
            )
            WHERE s.active = 1'
        );

        return $stores;
    }

    /**
     * Get hours for webservice
     *
     * @return string
     */
    public function getWsHours()
    {
        return $this->hours;
    }

    /**
     * Set hours for webservice
     *
     * @param string $hours
     *
     * @return bool
     */
    public function setWsHours($hours)
    {
        if (!is_string($hours)) {
            return false;
        }

        $this->hours = $hours;

        return true;
    }

    /**
     * This method is allow to know if a store exists for AdminImportController
     *
     * @return bool
     *
     * @since 1.7.0
     */
    public static function storeExists($idStore)
    {
        $row = Db::getInstance()->getRow('
            SELECT `id_store`
            FROM '._DB_PREFIX_.'store a
            WHERE a.`id_store` = '.(int) $idStore
        );

        return isset($row['id_store']);
    }
}
