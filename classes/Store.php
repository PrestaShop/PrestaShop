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
 * Class StoreCore.
 */
class StoreCore extends ObjectModel
{
    /** @var int Store id */
    public $id;

    /** @var int|bool Store id */
    public $id_image;

    /** @var int Country id */
    public $id_country;

    /** @var int State id */
    public $id_state;

    /** @var string|array<string> Name */
    public $name;

    /** @var string|array<string> Address first line */
    public $address1;

    /** @var string|array<string> Address second line (optional) */
    public $address2;

    /** @var string Postal code */
    public $postcode;

    /** @var string City */
    public $city;

    /** @var float Latitude */
    public $latitude;

    /** @var float Longitude */
    public $longitude;

    /** @var string|array Store hours (PHP serialized) */
    public $hours;

    /** @var string Phone number */
    public $phone;

    /** @var string Fax number */
    public $fax;

    /** @var string|array<string> Note */
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
    public static $definition = [
        'table' => 'store',
        'primary' => 'id_store',
        'multilang' => true,
        'fields' => [
            'id_country' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_state' => ['type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'],
            'postcode' => ['type' => self::TYPE_STRING, 'size' => 12],
            'city' => ['type' => self::TYPE_STRING, 'validate' => 'isCityName', 'required' => true, 'size' => 64],
            'latitude' => ['type' => self::TYPE_FLOAT, 'validate' => 'isCoordinate', 'size' => 13],
            'longitude' => ['type' => self::TYPE_FLOAT, 'validate' => 'isCoordinate', 'size' => 13],
            'phone' => ['type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 16],
            'fax' => ['type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 16],
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 255],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],

            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'address1' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isAddress', 'required' => true, 'size' => 255],
            'address2' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isAddress', 'size' => 255],
            'hours' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isJson', 'size' => 65000],
            'note' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 65000],
        ],
    ];

    protected $webserviceParameters = [
        'fields' => [
            'id_country' => ['xlink_resource' => 'countries'],
            'id_state' => ['xlink_resource' => 'states'],
            'hours' => ['getter' => 'getWsHours', 'setter' => 'setWsHours'],
        ],
    ];

    /**
     * StoreCore constructor.
     *
     * @param int|null $idStore
     * @param int|null $idLang
     */
    public function __construct($idStore = null, $idLang = null)
    {
        parent::__construct($idStore, $idLang);
        $this->id_image = ($this->id && file_exists(_PS_STORE_IMG_DIR_ . (int) $this->id . '.jpg')) ? (int) $this->id : false;
        $this->image_dir = _PS_STORE_IMG_DIR_;
    }

    /**
     * Get Stores by language.
     *
     * @param int $idLang
     *
     * @return array
     */
    public static function getStores($idLang)
    {
        return Db::getInstance()->executeS(
            'SELECT s.id_store AS `id`, s.*, sl.*
            FROM ' . _DB_PREFIX_ . 'store s  ' . Shop::addSqlAssociation('store', 's') . '
            LEFT JOIN ' . _DB_PREFIX_ . 'store_lang sl ON (sl.id_store = s.id_store AND sl.id_lang = ' . (int) $idLang . ')
            WHERE s.active = 1
            ORDER BY sl.`name` ASC'
        );
    }

    /**
     * Get hours for webservice.
     *
     * @return string
     */
    public function getWsHours()
    {
        return $this->hours;
    }

    /**
     * Set hours for webservice.
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
     * This method is allow to know if a store exists for AdminImportController.
     *
     * @return bool
     *
     * @since 1.7.0
     */
    public static function storeExists($idStore)
    {
        $row = Db::getInstance()->getRow(
            '
            SELECT `id_store`
            FROM ' . _DB_PREFIX_ . 'store a
            WHERE a.`id_store` = ' . (int) $idStore,
            false
        );

        return isset($row['id_store']);
    }
}
