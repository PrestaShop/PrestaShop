<?php
/**
 * 2007-2018 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class ManufacturerCore.
 */
class ManufacturerCore extends ObjectModel
{
    public $id;

    /** @var string Name */
    public $name;

    /** @var string A description */
    public $description;

    /** @var string A short description */
    public $short_description;

    /** @var int Address */
    public $id_address;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /** @var string Friendly URL */
    public $link_rewrite;

    /** @var string Meta title */
    public $meta_title;

    /** @var string Meta keywords */
    public $meta_keywords;

    /** @var string Meta description */
    public $meta_description;

    /** @var bool active */
    public $active;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'manufacturer',
        'primary' => 'id_manufacturer',
        'multilang' => true,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'required' => true, 'size' => 64),
            'active' => array('type' => self::TYPE_BOOL),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),

            /* Lang fields */
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'short_description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 512),
            'meta_keywords' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
        ),
    );

    protected $webserviceParameters = array(
        'fields' => array(
            'active' => array(),
            'link_rewrite' => array('getter' => 'getLink', 'setter' => false),
        ),
        'associations' => array(
            'addresses' => array(
                'resource' => 'address',
                'setter' => false,
                'fields' => array(
                    'id' => array('xlink_resource' => 'addresses'),
                ),
            ),
        ),
    );

    /**
     * ManufacturerCore constructor.
     *
     * @param int|null $id
     * @param int|null $idLang
     */
    public function __construct($id = null, $idLang = null)
    {
        parent::__construct($id, $idLang);

        $this->link_rewrite = $this->getLink();
        $this->image_dir = _PS_MANU_IMG_DIR_;
    }

    /**
     * Deletes current Manufacturer from the database.
     *
     * @return bool `true` if delete was successful
     *
     * @throws PrestaShopException
     */
    public function delete()
    {
        $address = new Address($this->id_address);

        if (Validate::isLoadedObject($address) && !$address->delete()) {
            return false;
        }

        if (parent::delete()) {
            CartRule::cleanProductRuleIntegrity('manufacturers', $this->id);

            return $this->deleteImage();
        }
    }

    /**
     * Delete several objects from database.
     *
     * return boolean Deletion result
     */
    public function deleteSelection($selection)
    {
        if (!is_array($selection)) {
            die(Tools::displayError());
        }

        $result = true;
        foreach ($selection as $id) {
            $this->id = (int) $id;
            $this->id_address = Manufacturer::getManufacturerAddress();
            $result = $result && $this->delete();
        }

        return $result;
    }

    /**
     * Get Manufacturer Address ID.
     *
     * @return bool|false|null|string
     */
    protected function getManufacturerAddress()
    {
        if (!(int) $this->id) {
            return false;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_address` FROM ' . _DB_PREFIX_ . 'address WHERE `id_manufacturer` = ' . (int) $this->id);
    }

    /**
     * Return manufacturers.
     *
     * @param bool $getNbProducts [optional] return products numbers for each
     * @param int $idLang Language ID
     * @param bool $active
     * @param int $p
     * @param int $n
     * @param bool $allGroup
     *
     * @return array Manufacturers
     */
    public static function getManufacturers($getNbProducts = false, $idLang = 0, $active = true, $p = false, $n = false, $allGroup = false, $group_by = false, $withProduct = false)
    {
        if (!$idLang) {
            $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        }
        if (!Group::isFeatureActive()) {
            $allGroup = true;
        }

        $manufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT m.*, ml.`description`, ml.`short_description`
		FROM `' . _DB_PREFIX_ . 'manufacturer` m'
        . Shop::addSqlAssociation('manufacturer', 'm') .
        'INNER JOIN `' . _DB_PREFIX_ . 'manufacturer_lang` ml ON (m.`id_manufacturer` = ml.`id_manufacturer` AND ml.`id_lang` = ' . (int) $idLang . ')' .
        'WHERE 1 ' .
        ($active ? 'AND m.`active` = 1 ' : '') .
        ($withProduct ? 'AND m.`id_manufacturer` IN (SELECT `id_manufacturer` FROM `' . _DB_PREFIX_ . 'product`) ' : '') .
        ($group_by ? ' GROUP BY m.`id_manufacturer`' : '') .
        'ORDER BY m.`name` ASC
		' . ($p ? ' LIMIT ' . (((int) $p - 1) * (int) $n) . ',' . (int) $n : ''));
        if ($manufacturers === false) {
            return false;
        }

        if ($getNbProducts) {
            $sqlGroups = '';
            if (!$allGroup) {
                $groups = FrontController::getCurrentCustomerGroups();
                $sqlGroups = (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '= 1');
            }

            $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
					SELECT  p.`id_manufacturer`, COUNT(DISTINCT p.`id_product`) as nb_products
					FROM `' . _DB_PREFIX_ . 'product` p USE INDEX (product_manufacturer)
					' . Shop::addSqlAssociation('product', 'p') . '
					LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` as m ON (m.`id_manufacturer`= p.`id_manufacturer`)
					WHERE p.`id_manufacturer` != 0 AND product_shop.`visibility` NOT IN ("none")
					' . ($active ? ' AND product_shop.`active` = 1 ' : '') . '
					' . (Group::isFeatureActive() && $allGroup ? '' : ' AND EXISTS (
						SELECT 1
						FROM `' . _DB_PREFIX_ . 'category_group` cg
						LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE p.`id_product` = cp.`id_product` AND cg.`id_group` ' . $sqlGroups . '
					)') . '
					GROUP BY p.`id_manufacturer`'
                );

            $counts = array();
            foreach ($results as $result) {
                $counts[(int) $result['id_manufacturer']] = (int) $result['nb_products'];
            }

            foreach ($manufacturers as $key => $manufacturer) {
                if (array_key_exists((int) $manufacturer['id_manufacturer'], $counts)) {
                    $manufacturers[$key]['nb_products'] = $counts[(int) $manufacturer['id_manufacturer']];
                } else {
                    $manufacturers[$key]['nb_products'] = 0;
                }
            }
        }

        $totalManufacturers = count($manufacturers);
        $rewriteSettings = (int) Configuration::get('PS_REWRITING_SETTINGS');
        for ($i = 0; $i < $totalManufacturers; ++$i) {
            $manufacturers[$i]['link_rewrite'] = ($rewriteSettings ? Tools::link_rewrite($manufacturers[$i]['name']) : 0);
        }

        return $manufacturers;
    }

    /**
     * List of manufacturers.
     *
     * @param int $idLang Specify the id of the language used
     *
     * @return array Manufacturers lite tree
     */
    public static function getLiteManufacturersList($idLang = null, $format = 'default')
    {
        $idLang = is_null($idLang) ? Context::getContext()->language->id : (int) $idLang;

        $manufacturersList = array();
        $manufacturers = Manufacturer::getManufacturers(false, $idLang);
        if ($manufacturers && count($manufacturers)) {
            foreach ($manufacturers as $manufacturer) {
                if ($format === 'sitemap') {
                    $manufacturersList[] = array(
                        'id' => 'manufacturer-page-' . (int) $manufacturer['id_manufacturer'],
                        'label' => $manufacturer['name'],
                        'url' => Context::getContext()->link->getManufacturerLink($manufacturer['id_manufacturer'], $manufacturer['link_rewrite']),
                        'children' => array(),
                    );
                } else {
                    $manufacturersList[] = array(
                        'id' => (int) $manufacturer['id_manufacturer'],
                        'link' => Context::getContext()->link->getManufacturerLink($manufacturer['id_manufacturer'], $manufacturer['link_rewrite']),
                        'name' => $manufacturer['name'],
                        'desc' => $manufacturer['description'],
                        'children' => array(),
                    );
                }
            }
        }

        return $manufacturersList;
    }

    /**
     * Return name from id.
     *
     * @param int $id_manufacturer Manufacturer ID
     *
     * @return string name
     */
    protected static $cacheName = array();

    public static function getNameById($idManufacturer)
    {
        if (!isset(self::$cacheName[$idManufacturer])) {
            self::$cacheName[$idManufacturer] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `name`
				FROM `' . _DB_PREFIX_ . 'manufacturer`
				WHERE `id_manufacturer` = ' . (int) $idManufacturer . '
				AND `active` = 1'
            );
        }

        return self::$cacheName[$idManufacturer];
    }

    /**
     * Get Manufacturer ID by name.
     *
     * @param string $name
     *
     * @return bool|int
     */
    public static function getIdByName($name)
    {
        $result = Db::getInstance()->getRow('
			SELECT `id_manufacturer`
			FROM `' . _DB_PREFIX_ . 'manufacturer`
			WHERE `name` = \'' . pSQL($name) . '\''
        );

        if (isset($result['id_manufacturer'])) {
            return (int) $result['id_manufacturer'];
        }

        return false;
    }

    /**
     * Get link to Manufacturer page.
     *
     * @return string
     */
    public function getLink()
    {
        return Tools::link_rewrite($this->name);
    }

    /**
     * Get Products by Manufacturer ID.
     *
     * @param int $idManufacturer
     * @param int $idLang
     * @param int $p
     * @param int $n
     * @param null $orderBy
     * @param null $orderWay
     * @param bool $getTotal
     * @param bool $active
     * @param bool $activeCategory
     * @param Context|null $context
     *
     * @return array|bool
     */
    public static function getProducts(
        $idManufacturer,
        $idLang,
        $p,
        $n,
        $orderBy = null,
        $orderWay = null,
        $getTotal = false,
        $active = true,
        $activeCategory = true,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        if ($p < 1) {
            $p = 1;
        }

        if (empty($orderBy) || $orderBy == 'position') {
            $orderBy = 'name';
        }

        if (empty($orderWay)) {
            $orderWay = 'ASC';
        }

        if (!Validate::isOrderBy($orderBy) || !Validate::isOrderWay($orderWay)) {
            die(Tools::displayError());
        }

        $groups = FrontController::getCurrentCustomerGroups();
        $sqlGroups = count($groups) ? 'IN (' . implode(',', $groups) . ')' : '= 1';

        /* Return only the number of products */
        if ($getTotal) {
            $sql = '
				SELECT p.`id_product`
				FROM `' . _DB_PREFIX_ . 'product` p
				' . Shop::addSqlAssociation('product', 'p') . '
				WHERE p.id_manufacturer = ' . (int) $idManufacturer
                . ($active ? ' AND product_shop.`active` = 1' : '') . '
				' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
				AND EXISTS (
					SELECT 1
					FROM `' . _DB_PREFIX_ . 'category_group` cg
					LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_category` = cg.`id_category`)' .
                    ($activeCategory ? ' INNER JOIN `' . _DB_PREFIX_ . 'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '') . '
					WHERE p.`id_product` = cp.`id_product` AND cg.`id_group` ' . $sqlGroups . '
				)';

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

            return (int) count($result);
        }
        if (strpos($orderBy, '.') > 0) {
            $orderBy = explode('.', $orderBy);
            $orderBy = pSQL($orderBy[0]) . '.`' . pSQL($orderBy[1]) . '`';
        }

        if ($orderBy == 'price') {
            $alias = 'product_shop.';
        } elseif ($orderBy == 'name') {
            $alias = 'pl.';
        } elseif ($orderBy == 'manufacturer_name') {
            $orderBy = 'name';
            $alias = 'm.';
        } elseif ($orderBy == 'quantity') {
            $alias = 'stock.';
        } else {
            $alias = 'p.';
        }

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity'
            . (Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.`id_product_attribute`,0) id_product_attribute' : '') . '
			, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`,
			pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
				DATEDIFF(
					product_shop.`date_add`,
					DATE_SUB(
						"' . date('Y-m-d') . ' 00:00:00",
						INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
					)
				) > 0 AS new'
            . ' FROM `' . _DB_PREFIX_ . 'product` p
			' . Shop::addSqlAssociation('product', 'p') .
            (Combination::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
						ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')' : '') . '
			LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
				ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('pl') . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
			LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
				ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $idLang . ')
			LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m
				ON (m.`id_manufacturer` = p.`id_manufacturer`)
			' . Product::sqlStock('p', 0);

        if (Group::isFeatureActive() || $activeCategory) {
            $sql .= 'JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (p.id_product = cp.id_product)';
            if (Group::isFeatureActive()) {
                $sql .= 'JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.`id_category` = cg.`id_category` AND cg.`id_group` ' . $sqlGroups . ')';
            }
            if ($activeCategory) {
                $sql .= 'JOIN `' . _DB_PREFIX_ . 'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1';
            }
        }

        $sql .= '
				WHERE p.`id_manufacturer` = ' . (int) $idManufacturer . '
				' . ($active ? ' AND product_shop.`active` = 1' : '') . '
				' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
				GROUP BY p.id_product
				ORDER BY ' . $alias . '`' . bqSQL($orderBy) . '` ' . pSQL($orderWay) . '
				LIMIT ' . (((int) $p - 1) * (int) $n) . ',' . (int) $n;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($orderBy == 'price') {
            Tools::orderbyPrice($result, $orderWay);
        }

        return Product::getProductsProperties($idLang, $result);
    }

    /**
     * Get Products by Manufacturer
     * (light edition).
     *
     * @param int $idLang
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getProductsLite($idLang)
    {
        $context = Context::getContext();
        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        return Db::getInstance()->executeS('
		SELECT p.`id_product`,  pl.`name`
		FROM `' . _DB_PREFIX_ . 'product` p
		' . Shop::addSqlAssociation('product', 'p') . '
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
			p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = ' . (int) $idLang . $context->shop->addSqlRestrictionOnLang('pl') . '
		)
		WHERE p.`id_manufacturer` = ' . (int) $this->id .
        ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : ''));
    }

    /**
     * Specify if a manufacturer already in base.
     *
     * @param int $idManufacturer Manufacturer id
     *
     * @return bool
     */
    public static function manufacturerExists($idManufacturer)
    {
        $row = Db::getInstance()->getRow('
			SELECT `id_manufacturer`
			FROM ' . _DB_PREFIX_ . 'manufacturer m
			WHERE m.`id_manufacturer` = ' . (int) $idManufacturer
        );

        return isset($row['id_manufacturer']);
    }

    /**
     * Get Manufacturer Addresses.
     *
     * @param int $idLang
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getAddresses($idLang)
    {
        return Db::getInstance()->executeS('
			SELECT a.*, cl.name AS `country`, s.name AS `state`
			FROM `' . _DB_PREFIX_ . 'address` AS a
			LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` AS cl ON (
				cl.`id_country` = a.`id_country`
				AND cl.`id_lang` = ' . (int) $idLang . '
			)
			LEFT JOIN `' . _DB_PREFIX_ . 'state` AS s ON (s.`id_state` = a.`id_state`)
			WHERE `id_manufacturer` = ' . (int) $this->id . '
			AND a.`deleted` = 0'
        );
    }

    /**
     * Get Manufacturer Addresses
     * (for webservice).
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getWsAddresses()
    {
        return Db::getInstance()->executeS('
			SELECT a.id_address as id
			FROM `' . _DB_PREFIX_ . 'address` AS a
			' . Shop::addSqlAssociation('manufacturer', 'a') . '
			WHERE a.`id_manufacturer` = ' . (int) $this->id . '
			AND a.`deleted` = 0'
        );
    }

    /**
     * Set Manufacturer Addresses
     * (for webservice).
     *
     * @param array $idAddresses
     *
     * @return bool
     */
    public function setWsAddresses($idAddresses)
    {
        $ids = array();

        foreach ($idAddresses as $id) {
            $ids[] = (int) $id['id'];
        }

        $result1 = (Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'address`
			SET id_manufacturer = 0
			WHERE id_manufacturer = ' . (int) $this->id . '
			AND deleted = 0') !== false
        );

        $result2 = true;
        if (count($ids)) {
            $result2 = (Db::getInstance()->execute('
				UPDATE `' . _DB_PREFIX_ . 'address`
				SET id_customer = 0, id_supplier = 0, id_manufacturer = ' . (int) $this->id . '
				WHERE id_address IN(' . implode(',', $ids) . ')
				AND deleted = 0') !== false
            );
        }

        return $result1 && $result2;
    }
}
