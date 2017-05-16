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
 * Class MetaCore
 */
class MetaCore extends ObjectModel
{
    public $page;
    public $configurable = 1;
    public $title;
    public $description;
    public $keywords;
    public $url_rewrite;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'meta',
        'primary' => 'id_meta',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'page' => array('type' => self::TYPE_STRING, 'validate' => 'isFileName', 'required' => true, 'size' => 64),
            'configurable' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),

            /* Lang fields */
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128),
            'description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'keywords' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'url_rewrite' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'size' => 255),
        ),
    );

    /**
     * Get pages
     *
     * @param bool $excludeFilled
     * @param bool $addPage
     *
     * @return array
     */
    public static function getPages($excludeFilled = false, $addPage = false)
    {
        $selectedPages = array();
        if (!$files = Tools::scandir(_PS_CORE_DIR_.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR, 'php', '', true)) {
            die(Context::getContext()->getTranslator()->trans('Cannot scan root directory', array(), 'Admin.Notifications.Error'));
        }

        if (!$overrideFiles = Tools::scandir(_PS_CORE_DIR_.DIRECTORY_SEPARATOR . 'override' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR, 'php', '', true)) {
            die(Context::getContext()->getTranslator()->trans('Cannot scan "override" directory', array(), 'Admin.Notifications.Error'));
        }

        $files = array_values(array_unique(array_merge($files, $overrideFiles)));

        // Exclude pages forbidden
        $exludePages = array(
            'category',
            'changecurrency',
            'cms',
            'footer',
            'header',
            'pagination',
            'product',
            'product-sort',
            'statistics',
        );

        foreach ($files as $file) {
            if ($file != 'index.php' && !in_array(strtolower(str_replace('Controller.php', '', $file)), $exludePages)) {
                $className = str_replace('.php', '', $file);
                $reflection = class_exists($className) ? new ReflectionClass(str_replace('.php', '', $file)) : false;
                $properties = $reflection ? $reflection->getDefaultProperties() : array();
                if (isset($properties['php_self'])) {
                    $selectedPages[$properties['php_self']] = $properties['php_self'];
                } elseif (preg_match('/^[a-z0-9_.-]*\.php$/i', $file)) {
                    $selectedPages[strtolower(str_replace('Controller.php', '', $file))] = strtolower(str_replace('Controller.php', '', $file));
                } elseif (preg_match('/^([a-z0-9_.-]*\/)?[a-z0-9_.-]*\.php$/i', $file)) {
                    $selectedPages[strtolower(Context::getContext()->getTranslator()->trans('File %2$s (in directory %1$s)', array(dirname($file), str_replace('Controller.php', '', basename($file))), 'Admin.Notifications.Error'))] = strtolower(str_replace('Controller.php', '', basename($file)));
                }
            }
        }

        // Add modules controllers to list (this function is cool !)
        foreach (glob(_PS_MODULE_DIR_.'*/controllers/front/*.php') as $file) {
            $filename = Tools::strtolower(basename($file, '.php'));
            if ($filename == 'index') {
                continue;
            }

            $module = Tools::strtolower(basename(dirname(dirname(dirname($file)))));
            $selectedPages[$module.' - '.$filename] = 'module-'.$module.'-'.$filename;
        }

        // Exclude page already filled
        if ($excludeFilled) {
            $metas = Meta::getMetas();
            foreach ($metas as $meta) {
                if (in_array($meta['page'], $selectedPages)) {
                    unset($selectedPages[array_search($meta['page'], $selectedPages)]);
                }
            }
        }
        // Add selected page
        if ($addPage) {
            $name = $addPage;
            if (preg_match('#module-([a-z0-9_-]+)-([a-z0-9]+)$#i', $addPage, $m)) {
                $addPage = $m[1].' - '.$m[2];
            }
            $selectedPages[$addPage] = $name;
            asort($selectedPages);
        }

        return $selectedPages;
    }

    /**
     * Get all Metas
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getMetas()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'meta ORDER BY page ASC');
    }

    /**
     * Get all metas, but filter by Language
     *
     * @param int $idLang Language ID
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getMetasByIdLang($idLang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'meta` m
		LEFT JOIN `'._DB_PREFIX_.'meta_lang` ml ON m.`id_meta` = ml.`id_meta`
		WHERE ml.`id_lang` = '.(int) $idLang
            .Shop::addSqlRestrictionOnLang('ml').
        'ORDER BY page ASC');
    }

    /**
     * Get metas by page
     *
     * @param string $page
     * @param int    $idLang Language ID
     *
     * @return array|bool|null|object
     */
    public static function getMetaByPage($page, $idLang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        		SELECT *
        		FROM '._DB_PREFIX_.'meta m
        		LEFT JOIN '._DB_PREFIX_.'meta_lang ml ON m.id_meta = ml.id_meta
        		WHERE (
        			m.page = "'.pSQL($page).'"
        			OR m.page = "'.pSQL(str_replace('-', '', strtolower($page))).'"
        		)
        		AND ml.id_lang = '.(int) $idLang.'
		'.Shop::addSqlRestrictionOnLang('ml'));
    }

    /**
     * Get all metas
     *
     * @param int $idLang
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getAllMeta($idLang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM '._DB_PREFIX_.'meta m
		LEFT JOIN '._DB_PREFIX_.'meta_lang ml ON m.id_meta = ml.id_meta
		AND ml.id_lang = '.(int) $idLang.'
		'.Shop::addSqlRestrictionOnLang('ml'));
    }

    /**
     * Updates the current Meta in the database
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Meta has been successfully updated
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        if (!parent::update($nullValues)) {
            return false;
        }

        return Tools::generateHtaccess();
    }

    /**
     * Deletes current Meta from the database
     *
     * @return bool `true` if delete was successful
     * @throws PrestaShopException
     */
    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }

        return Tools::generateHtaccess();
    }

    /**
     * Delete selection
     *
     * @param array $selection
     *
     * @return bool
     */
    public function deleteSelection($selection)
    {
        if (!is_array($selection)) {
            die(Tools::displayError());
        }
        $result = true;
        foreach ($selection as $id) {
            $this->id = (int) $id;
            $result = $result && $this->delete();
        }

        return $result && Tools::generateHtaccess();
    }

    /**
     * Get equivalent URL rewrite
     *
     * @param int    $newIdLang
     * @param int    $idLang
     * @param string $urlRewrite
     *
     * @return false|null|string
     */
    public static function getEquivalentUrlRewrite($newIdLang, $idLang, $urlRewrite)
    {
        return Db::getInstance()->getValue('
		SELECT url_rewrite
		FROM `'._DB_PREFIX_.'meta_lang`
		WHERE id_meta = (
			SELECT id_meta
			FROM `'._DB_PREFIX_.'meta_lang`
			WHERE url_rewrite = \''.pSQL($urlRewrite).'\' AND id_lang = '.(int) $idLang.'
			AND id_shop = '.Context::getContext()->shop->id.'
		)
		AND id_lang = '.(int) $newIdLang.'
		AND id_shop = '.Context::getContext()->shop->id);
    }

    /**
     * Get meta tags
     *
     * @since 1.5.0
     */
    public static function getMetaTags($idLang, $pageName, $title = '')
    {
        if (Configuration::get('PS_SHOP_ENABLE')
            || in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('PS_MAINTENANCE_IP')))) {
            if ($pageName == 'product' && ($idProduct = Tools::getValue('id_product'))) {
                return Meta::getProductMetas($idProduct, $idLang, $pageName);
            } elseif ($pageName == 'category' && ($idCategory = Tools::getValue('id_category'))) {
                return Meta::getCategoryMetas($idCategory, $idLang, $pageName, $title);
            } elseif ($pageName == 'manufacturer' && ($idManufacturer = Tools::getValue('id_manufacturer'))) {
                return Meta::getManufacturerMetas($idManufacturer, $idLang, $pageName);
            } elseif ($pageName == 'supplier' && ($idSupplier = Tools::getValue('id_supplier'))) {
                return Meta::getSupplierMetas($idSupplier, $idLang, $pageName);
            } elseif ($pageName == 'cms' && ($idCms = Tools::getValue('id_cms'))) {
                return Meta::getCmsMetas($idCms, $idLang, $pageName);
            } elseif ($pageName == 'cms' && ($idCmsCategory = Tools::getValue('id_cms_category'))) {
                return Meta::getCmsCategoryMetas($idCmsCategory, $idLang, $pageName);
            }
        }
        return Meta::getHomeMetas($idLang, $pageName);
    }

    /**
     * Get meta tags for a given page
     *
     * @param int    $idLang   Language ID
     * @param string $pageName Page name
     *
     * @return array Meta tags
     *
     * @since 1.5.0
     */
    public static function getHomeMetas($idLang, $pageName)
    {
        $metas = Meta::getMetaByPage($pageName, $idLang);
        $ret['meta_title'] = (isset($metas['title']) && $metas['title']) ? $metas['title'] : Configuration::get('PS_SHOP_NAME');
        $ret['meta_description'] = (isset($metas['description']) && $metas['description']) ? $metas['description'] : '';
        $ret['meta_keywords'] = (isset($metas['keywords']) && $metas['keywords']) ? $metas['keywords'] :  '';

        return $ret;
    }

    /**
     * Get product meta tags
     *
     * @param int          $idProduct
     * @param int    $idLang
     * @param string $pageName
     *
     * @return array
     *
     * @since 1.5.0
     */
    public static function getProductMetas($idProduct, $idLang, $pageName)
    {
        $sql = 'SELECT `name`, `meta_title`, `meta_description`, `meta_keywords`, `description_short`
				FROM `'._DB_PREFIX_.'product` p
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = p.`id_product`'.Shop::addSqlRestrictionOnLang('pl').')
				'.Shop::addSqlAssociation('product', 'p').'
				WHERE pl.id_lang = '.(int) $idLang.'
					AND pl.id_product = '.(int) $idProduct.'
					AND product_shop.active = 1';
        if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
            if (empty($row['meta_description'])) {
                $row['meta_description'] = strip_tags($row['description_short']);
            }

            return Meta::completeMetaTags($row, $row['name']);
        }

        return Meta::getHomeMetas($idLang, $pageName);
    }

    /**
     * Get category meta tags
     *
     * @param int    $idCategory
     * @param int    $idLang
     * @param string $pageName
     *
     * @return array
     *
     * @since 1.5.0
     */
    public static function getCategoryMetas($idCategory, $idLang, $pageName, $title = '')
    {
        if (!empty($title)) {
            $title = ' - '.$title;
        }
        $pageNumber = (int)Tools::getValue('p');
        $sql = 'SELECT `name`, `meta_title`, `meta_description`, `meta_keywords`, `description`
				FROM `'._DB_PREFIX_.'category_lang` cl
				WHERE cl.`id_lang` = '.(int) $idLang.'
					AND cl.`id_category` = '.(int) $idCategory.Shop::addSqlRestrictionOnLang('cl');

        $cacheId = 'Meta::getCategoryMetas'.(int) $idCategory.'-'.(int) $idLang;
        if (!Cache::isStored($cacheId)) {
            if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
                if (empty($row['meta_description'])) {
                    $row['meta_description'] = strip_tags($row['description']);
                }

                // Paginate title
                if (!empty($row['meta_title'])) {
                    $row['meta_title'] = $title.$row['meta_title'].(!empty($pageNumber) ? ' ('.$pageNumber.')' : '');
                } else {
                    $row['meta_title'] = $row['name'].(!empty($pageNumber) ? ' ('.$pageNumber.')' : '');
                }

                if (!empty($title)) {
                    $row['meta_title'] = $title.(!empty($pageNumber) ? ' ('.$pageNumber.')' : '');
                }

                $result = Meta::completeMetaTags($row, $row['name']);
            } else {
                $result = Meta::getHomeMetas($idLang, $pageName);
            }
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Get manufacturer meta tags
     *
     *
     * @param int    $idManufacturer
     * @param int    $idLang
     * @param string $pageName
     *
     * @return array
     *
     * @since 1.5.0
     */
    public static function getManufacturerMetas($idManufacturer, $idLang, $pageName)
    {
        $pageNumber = (int)Tools::getValue('p');
        $sql = 'SELECT `name`, `meta_title`, `meta_description`, `meta_keywords`
				FROM `'._DB_PREFIX_.'manufacturer_lang` ml
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (ml.`id_manufacturer` = m.`id_manufacturer`)
				WHERE ml.id_lang = '.(int)$idLang.'
					AND ml.id_manufacturer = '.(int) $idManufacturer;
        if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
            if (!empty($row['meta_description'])) {
                $row['meta_description'] = strip_tags($row['meta_description']);
            }
            $row['meta_title'] = ($row['meta_title'] ? $row['meta_title'] : $row['name']).(!empty($pageNumber) ? ' ('.$pageNumber.')' : '');
            $row['meta_title'];

            return Meta::completeMetaTags($row, $row['meta_title']);
        }

        return Meta::getHomeMetas($idLang, $pageName);
    }

    /**
     * Get supplier meta tags
     *
     *
     * @param int    $idSupplier
     * @param int    $idLang
     * @param string $pageName
     *
     * @return array
     *
     * @since 1.5.0
     */
    public static function getSupplierMetas($idSupplier, $idLang, $pageName)
    {
        $sql = 'SELECT `name`, `meta_title`, `meta_description`, `meta_keywords`
				FROM `'._DB_PREFIX_.'supplier_lang` sl
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (sl.`id_supplier` = s.`id_supplier`)
				WHERE sl.id_lang = '.(int)$idLang.'
					AND sl.id_supplier = '.(int)$idSupplier;
        if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
            if (!empty($row['meta_description'])) {
                $row['meta_description'] = strip_tags($row['meta_description']);
            }
            if (!empty($row['meta_title'])) {
                $row['meta_title'] = $row['meta_title'];
            }
            return Meta::completeMetaTags($row, $row['name']);
        }

        return Meta::getHomeMetas($idLang, $pageName);
    }

    /**
     * Get CMS meta tags
     *
     * @param int          $idCms
     * @param int    $idLang
     * @param string $pageName
     *
     * @return array
     *
     * @since 1.5.0
     */
    public static function getCmsMetas($idCms, $idLang, $pageName)
    {
        $sql = 'SELECT `meta_title`, `meta_description`, `meta_keywords`
				FROM `'._DB_PREFIX_.'cms_lang`
				WHERE id_lang = '.(int) $idLang.'
					AND id_cms = '.(int) $idCms.
                    ((int) Context::getContext()->shop->id ?
                        ' AND id_shop = '.(int) Context::getContext()->shop->id : '');

        if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
            $row['meta_title'] = $row['meta_title'];

            return Meta::completeMetaTags($row, $row['meta_title']);
        }

        return Meta::getHomeMetas($idLang, $pageName);
    }

    /**
     * Get CMS category meta tags
     *
     * @param int    $idCmsCategory
     * @param int    $idLang
     * @param string $pageName
     *
     * @return array
     *
     * @since 1.5.0
     */
    public static function getCmsCategoryMetas($idCmsCategory, $idLang, $pageName)
    {
        $sql = 'SELECT `name`, `meta_title`, `meta_description`, `meta_keywords`
				FROM `'._DB_PREFIX_.'cms_category_lang`
				WHERE id_lang = '.(int) $idLang.'
					AND id_cms_category = '.(int) $idCmsCategory.
            ((int) Context::getContext()->shop->id ?
            ' AND id_shop = '.(int)Context::getContext()->shop->id : '');
        if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
            $row['meta_title'] = empty($row['meta_title']) ? $row['name'] : $row['meta_title'];
            return Meta::completeMetaTags($row, $row['meta_title']);
        }

        return Meta::getHomeMetas($idLang, $pageName);
    }

    /**
     * @since 1.5.0
     */
    public static function completeMetaTags($metaTags, $defaultValue, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        if (empty($metaTags['meta_title'])) {
            $metaTags['meta_title'] = $defaultValue;
        }

        return $metaTags;
    }
}
