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
 * Class GuestCore.
 */
class GuestCore extends ObjectModel
{
    public $id_operating_system;
    public $id_web_browser;
    public $id_customer;
    public $javascript;
    public $screen_resolution_x;
    public $screen_resolution_y;
    public $screen_color;
    public $sun_java;
    public $adobe_flash;
    public $adobe_director;
    public $apple_quicktime;
    public $real_player;
    public $windows_media;
    public $accept_language;
    public $mobile_theme;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'guest',
        'primary' => 'id_guest',
        'fields' => [
            'id_operating_system' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_web_browser' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'javascript' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'screen_resolution_x' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'screen_resolution_y' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'screen_color' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'sun_java' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'adobe_flash' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'adobe_director' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'apple_quicktime' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'real_player' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'windows_media' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'accept_language' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 8],
            'mobile_theme' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    protected $webserviceParameters = [
        'fields' => [
            'id_customer' => ['xlink_resource' => 'customers'],
        ],
    ];

    /**
     * Set user agent.
     */
    public function userAgent()
    {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $acceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        $this->accept_language = $this->getLanguage($acceptLanguage);
        $this->id_operating_system = $this->getOs($userAgent);
        $this->id_web_browser = $this->getBrowser($userAgent);
        $this->mobile_theme = Context::getContext()->getMobileDevice();
    }

    /**
     * Get Guest Language.
     *
     * @param string $acceptLanguage
     *
     * @return mixed|string
     */
    protected function getLanguage($acceptLanguage)
    {
        // $langsArray is filled with all the languages accepted, ordered by priority
        $langsArray = [];
        preg_match_all('/([a-z]{2}(-[a-z]{2})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/', $acceptLanguage, $array);
        if (count($array[1])) {
            $langsArray = array_combine($array[1], $array[4]);
            foreach ($langsArray as $lang => $val) {
                if ($val === '') {
                    $langsArray[$lang] = 1;
                }
            }
            arsort($langsArray, SORT_NUMERIC);
        }

        // Only the first language is returned
        return count($langsArray) ? key($langsArray) : '';
    }

    /**
     * Get browser.
     *
     * @param string $userAgent
     */
    protected function getBrowser($userAgent)
    {
        $browserArray = [
            'Chrome' => 'Chrome/',
            'Safari' => 'Safari',
            'Safari iPad' => 'iPad',
            'Firefox' => 'Firefox/',
            'Opera' => 'Opera',
            'IE 11' => 'Trident',
            'IE 10' => 'MSIE 10',
            'IE 9' => 'MSIE 9',
            'IE 8' => 'MSIE 8',
            'IE 7' => 'MSIE 7',
            'IE 6' => 'MSIE 6',
        ];
        foreach ($browserArray as $k => $value) {
            if (strstr($userAgent, $value)) {
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT `id_web_browser`
				FROM `' . _DB_PREFIX_ . 'web_browser` wb
				WHERE wb.`name` = \'' . pSQL($k) . '\'');

                return $result['id_web_browser'] ?? null;
            }
        }

        return null;
    }

    /**
     * Get OS.
     *
     * @param string $userAgent
     */
    protected function getOs($userAgent)
    {
        $osArray = [
            'Windows 10' => 'Windows NT 10',
            'Windows 8.1' => 'Windows NT 6.3',
            'Windows 8' => 'Windows NT 6.2',
            'Windows 7' => 'Windows NT 6.1',
            'Windows Vista' => 'Windows NT 6.0',
            'Windows XP' => 'Windows NT 5',
            'MacOsX' => 'Mac OS X',
            'Android' => 'Android',
            'Linux' => 'X11',
        ];

        foreach ($osArray as $k => $value) {
            if (strstr($userAgent, $value)) {
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT `id_operating_system`
				FROM `' . _DB_PREFIX_ . 'operating_system` os
				WHERE os.`name` = \'' . pSQL($k) . '\'');

                return $result['id_operating_system'] ?? null;
            }
        }

        return null;
    }

    /**
     * Get Guest ID from Customer ID.
     *
     * @param int $idCustomer Customer ID
     *
     * @return bool|int
     */
    public static function getFromCustomer($idCustomer)
    {
        if (!Validate::isUnsignedId($idCustomer)) {
            return false;
        }
        $result = Db::getInstance()->getRow('
		SELECT `id_guest`
		FROM `' . _DB_PREFIX_ . 'guest`
		WHERE `id_customer` = ' . (int) ($idCustomer));

        return $result['id_guest'] ?? false;
    }

    /**
     * Merge with Customer.
     *
     * @param int $idGuest Guest ID
     * @param int $idCustomer Customer ID
     *
     * @return bool
     */
    public function mergeWithCustomer($idGuest, $idCustomer)
    {
        // Since the guests are merged, the guest id in the connections table must be changed too
        Db::getInstance()->update('connections', [
            'id_guest' => (int) $idGuest,
        ], 'id_guest = ' . (int) $this->id);

        // Since the guests are merged, the guest id in the cart table must be changed too
        Db::getInstance()->update('cart', [
            'id_guest' => (int) $idGuest,
        ], 'id_guest = ' . (int) $this->id);

        // The existing guest is removed from the database
        $existingGuest = new Guest((int) $idGuest);
        $existingGuest->delete();

        // The current guest is removed from the database
        $this->delete();

        // $this is still filled with values, so it's id is changed for the old guest
        $this->id = (int) $idGuest;
        $this->id_customer = (int) $idCustomer;

        // $this is now the old guest but filled with the most up to date values
        $this->force_id = true;

        return $this->add();
    }

    /**
     * Set new guest.
     *
     * @param CookieCore $cookie
     */
    public static function setNewGuest($cookie)
    {
        $guest = new Guest(isset($cookie->id_customer) ? (int) Guest::getFromCustomer((int) ($cookie->id_customer)) : null);
        $guest->userAgent();
        $guest->save();
        $cookie->id_guest = (int) ($guest->id);
    }
}
