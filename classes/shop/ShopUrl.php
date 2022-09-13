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
class ShopUrlCore extends ObjectModel
{
    public $id_shop;
    public $domain;
    public $domain_ssl;
    public $physical_uri;
    public $virtual_uri;
    public $main;
    public $active;

    protected static $main_domain = [];
    protected static $main_domain_ssl = [];

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'shop_url',
        'primary' => 'id_shop_url',
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'main' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'domain' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 255, 'validate' => 'isCleanHtml'],
            'domain_ssl' => ['type' => self::TYPE_STRING, 'size' => 255, 'validate' => 'isCleanHtml'],
            'id_shop' => ['type' => self::TYPE_INT, 'required' => true],
            'physical_uri' => ['type' => self::TYPE_STRING, 'size' => 64],
            'virtual_uri' => ['type' => self::TYPE_STRING, 'size' => 64],
        ],
    ];

    protected $webserviceParameters = [
        'fields' => [
            'id_shop' => ['xlink_resource' => 'shops'],
        ],
    ];

    /**
     * @see ObjectModel::getFields()
     *
     * @return array
     */
    public function getFields()
    {
        $this->domain = trim($this->domain);
        $this->domain_ssl = trim($this->domain_ssl);
        $this->physical_uri = trim(str_replace(' ', '', $this->physical_uri), '/');

        if ($this->physical_uri) {
            $this->physical_uri = preg_replace('#/+#', '/', '/' . $this->physical_uri . '/');
        } else {
            $this->physical_uri = '/';
        }

        $this->virtual_uri = trim(str_replace(' ', '', $this->virtual_uri ?? ''), '/');
        if ($this->virtual_uri) {
            $this->virtual_uri = preg_replace('#/+#', '/', trim($this->virtual_uri, '/')) . '/';
        }

        return parent::getFields();
    }

    public function getBaseURI()
    {
        return $this->physical_uri . $this->virtual_uri;
    }

    public function getURL($ssl = false)
    {
        if (!$this->id) {
            return;
        }

        $url = ($ssl) ? 'https://' . $this->domain_ssl : 'http://' . $this->domain;

        return $url . $this->getBaseURI();
    }

    /**
     * Get list of shop urls.
     *
     * @param int|bool $id_shop
     *
     * @return PrestaShopCollection Collection of ShopUrl
     */
    public static function getShopUrls($id_shop = false)
    {
        $urls = new PrestaShopCollection('ShopUrl');
        if ($id_shop) {
            $urls->where('id_shop', '=', $id_shop);
        }

        return $urls;
    }

    public function setMain()
    {
        $res = Db::getInstance()->update('shop_url', ['main' => 0], 'id_shop = ' . (int) $this->id_shop);
        $res &= Db::getInstance()->update('shop_url', ['main' => 1], 'id_shop_url = ' . (int) $this->id);
        $this->main = true;

        // Reset main URL for all shops to prevent problems
        $sql = 'SELECT s1.id_shop_url FROM ' . _DB_PREFIX_ . 'shop_url s1
                WHERE (
                    SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'shop_url s2
                    WHERE s2.main = 1
                    AND s2.id_shop = s1.id_shop
                ) = 0
                GROUP BY s1.id_shop';
        foreach (Db::getInstance()->executeS($sql) as $row) {
            Db::getInstance()->update('shop_url', ['main' => 1], 'id_shop_url = ' . $row['id_shop_url']);
        }

        return $res;
    }

    public function canAddThisUrl($domain, $domain_ssl, $physical_uri, $virtual_uri)
    {
        $physical_uri = trim($physical_uri, '/');

        if ($physical_uri) {
            $physical_uri = preg_replace('#/+#', '/', '/' . $physical_uri . '/');
        } else {
            $physical_uri = '/';
        }

        $virtual_uri = trim($virtual_uri, '/');
        if ($virtual_uri) {
            $virtual_uri = preg_replace('#/+#', '/', trim($virtual_uri, '/')) . '/';
        }

        $sql = 'SELECT id_shop_url
                FROM ' . _DB_PREFIX_ . 'shop_url
                WHERE physical_uri = \'' . pSQL($physical_uri) . '\'
                    AND virtual_uri = \'' . pSQL($virtual_uri) . '\'
                    AND (domain = \'' . pSQL($domain) . '\' ' . (($domain_ssl) ? ' OR domain_ssl = \'' . pSQL($domain_ssl) . '\'' : '') . ')'
                    . ($this->id ? ' AND id_shop_url != ' . (int) $this->id : '');

        return Db::getInstance()->getValue($sql);
    }

    public static function cacheMainDomainForShop($id_shop)
    {
        if (!isset(self::$main_domain_ssl[(int) $id_shop]) || !isset(self::$main_domain[(int) $id_shop])) {
            // May be called while the context is not instanciated yet
            // For instance in first step of the installer
            if ($id_shop === null && !isset(Context::getContext()->shop)) {
                self::$main_domain[(int) $id_shop] = null;
                self::$main_domain_ssl[(int) $id_shop] = null;

                return;
            }

            $row = Db::getInstance()->getRow('
            SELECT domain, domain_ssl
            FROM ' . _DB_PREFIX_ . 'shop_url
            WHERE main = 1
            AND id_shop = ' . ($id_shop !== null ? (int) $id_shop : (int) Context::getContext()->shop->id));
            if (!empty($row)) {
                self::$main_domain[(int) $id_shop] = $row['domain'];
                self::$main_domain_ssl[(int) $id_shop] = $row['domain_ssl'];
            }
        }
    }

    public static function resetMainDomainCache()
    {
        self::$main_domain = [];
        self::$main_domain_ssl = [];
    }

    public static function getMainShopDomain($id_shop = null)
    {
        ShopUrl::cacheMainDomainForShop($id_shop);

        return self::$main_domain[(int) $id_shop];
    }

    public static function getMainShopDomainSSL($id_shop = null)
    {
        ShopUrl::cacheMainDomainForShop($id_shop);

        return self::$main_domain_ssl[(int) $id_shop];
    }
}
