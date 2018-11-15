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
 * Class CMSCore.
 */
class CMSCore extends ObjectModel
{
    /** @var string Name */
    public $id;
    public $id_cms;
    public $head_seo_title;
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    public $content;
    public $link_rewrite;
    public $id_cms_category;
    public $position;
    public $indexation;
    public $active;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'cms',
        'primary' => 'id_cms',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'id_cms_category' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'position' => array('type' => self::TYPE_INT),
            'indexation' => array('type' => self::TYPE_BOOL),
            'active' => array('type' => self::TYPE_BOOL),

            /* Lang fields */
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 512),
            'meta_keywords' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
            'head_seo_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'link_rewrite' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 128),
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 3999999999999),
        ),
    );

    protected $webserviceParameters = array(
        'objectNodeName' => 'content',
        'objectsNodeName' => 'content_management_system',
    );

    /**
     * Adds current CMS as a new Object to the database.
     *
     * @param bool $autoDate Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the CMS has been successfully added
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = false)
    {
        $this->position = CMS::getLastPosition((int) $this->id_cms_category);

        return parent::add($autoDate, true);
    }

    /**
     * Updates the current CMS in the database.
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the CMS has been successfully updated
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        if (parent::update($nullValues)) {
            return $this->cleanPositions($this->id_cms_category);
        }

        return false;
    }

    /**
     * Deletes current CMS from the database.
     *
     * @return bool True if delete was successful
     *
     * @throws PrestaShopException
     */
    public function delete()
    {
        if (parent::delete()) {
            return $this->cleanPositions($this->id_cms_category);
        }

        return false;
    }

    /**
     * Get links.
     *
     * @param int $idLang Language ID
     * @param null $selection
     * @param bool $active
     * @param Link|null $link
     *
     * @return array
     */
    public static function getLinks($idLang, $selection = null, $active = true, Link $link = null)
    {
        if (!$link) {
            $link = Context::getContext()->link;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.id_cms, cl.link_rewrite, cl.meta_title
		FROM ' . _DB_PREFIX_ . 'cms c
		LEFT JOIN ' . _DB_PREFIX_ . 'cms_lang cl ON (c.id_cms = cl.id_cms AND cl.id_lang = ' . (int) $idLang . ')
		' . Shop::addSqlAssociation('cms', 'c') . '
		WHERE 1
		' . (($selection !== null) ? ' AND c.id_cms IN (' . implode(',', array_map('intval', $selection)) . ')' : '') .
        ($active ? ' AND c.`active` = 1 ' : '') .
        'GROUP BY c.id_cms
		ORDER BY c.`position`');

        $links = array();
        if ($result) {
            foreach ($result as $row) {
                $row['link'] = $link->getCMSLink((int) $row['id_cms'], $row['link_rewrite']);
                $links[] = $row;
            }
        }

        return $links;
    }

    /**
     * @param null $idLang
     * @param bool $idBlock
     * @param bool $active
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function listCms($idLang = null, $idBlock = false, $active = true)
    {
        if (empty($idLang)) {
            $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.id_cms, l.meta_title
		FROM  ' . _DB_PREFIX_ . 'cms c
		JOIN ' . _DB_PREFIX_ . 'cms_lang l ON (c.id_cms = l.id_cms)
		' . Shop::addSqlAssociation('cms', 'c') . '
		' . (($idBlock) ? 'JOIN ' . _DB_PREFIX_ . 'block_cms b ON (c.id_cms = b.id_cms)' : '') . '
		WHERE l.id_lang = ' . (int) $idLang . (($idBlock) ? ' AND b.id_block = ' . (int) $idBlock : '') . ($active ? ' AND c.`active` = 1 ' : '') . '
		GROUP BY c.id_cms
		ORDER BY c.`position`');
    }

    /**
     * @param $way
     * @param $position
     *
     * @return bool
     */
    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
			SELECT cp.`id_cms`, cp.`position`, cp.`id_cms_category`
			FROM `' . _DB_PREFIX_ . 'cms` cp
			WHERE cp.`id_cms_category` = ' . (int) $this->id_cms_category . '
			ORDER BY cp.`position` ASC'
        )) {
            return false;
        }

        foreach ($res as $cms) {
            if ((int) $cms['id_cms'] == (int) $this->id) {
                $movedCms = $cms;
            }
        }

        if (!isset($movedCms) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'cms`
			SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
			WHERE `position`
			' . ($way
                ? '> ' . (int) $movedCms['position'] . ' AND `position` <= ' . (int) $position
                : '< ' . (int) $movedCms['position'] . ' AND `position` >= ' . (int) $position) . '
			AND `id_cms_category`=' . (int) $movedCms['id_cms_category'])
        && Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'cms`
			SET `position` = ' . (int) $position . '
			WHERE `id_cms` = ' . (int) $movedCms['id_cms'] . '
			AND `id_cms_category`=' . (int) $movedCms['id_cms_category']);
    }

    /**
     * @param $idCategory
     *
     * @return bool
     */
    public static function cleanPositions($idCategory)
    {
        $sql = '
		SELECT `id_cms`
		FROM `' . _DB_PREFIX_ . 'cms`
		WHERE `id_cms_category` = ' . (int) $idCategory . '
		ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);

        for ($i = 0, $total = count($result); $i < $total; ++$i) {
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'cms`
					SET `position` = ' . (int) $i . '
					WHERE `id_cms_category` = ' . (int) $idCategory . '
						AND `id_cms` = ' . (int) $result[$i]['id_cms'];
            Db::getInstance()->execute($sql);
        }

        return true;
    }

    /**
     * @param $idCategory
     *
     * @return false|null|string
     */
    public static function getLastPosition($idCategory)
    {
        $sql = '
		SELECT MAX(position) + 1
		FROM `' . _DB_PREFIX_ . 'cms`
		WHERE `id_cms_category` = ' . (int) $idCategory;

        return Db::getInstance()->getValue($sql);
    }

    /**
     * @param null $idLang
     * @param null $idCmsCategory
     * @param bool $active
     * @param null $idShop
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getCMSPages($idLang = null, $idCmsCategory = null, $active = true, $idShop = null)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('cms', 'c');

        if ($idLang) {
            if ($idShop) {
                $sql->innerJoin('cms_lang', 'l', 'c.id_cms = l.id_cms AND l.id_lang = ' . (int) $idLang . ' AND l.id_shop = ' . (int) $idShop);
            } else {
                $sql->innerJoin('cms_lang', 'l', 'c.id_cms = l.id_cms AND l.id_lang = ' . (int) $idLang);
            }
        }

        if ($idShop) {
            $sql->innerJoin('cms_shop', 'cs', 'c.id_cms = cs.id_cms AND cs.id_shop = ' . (int) $idShop);
        }

        if ($active) {
            $sql->where('c.active = 1');
        }

        if ($idCmsCategory) {
            $sql->where('c.id_cms_category = ' . (int) $idCmsCategory);
        }

        $sql->orderBy('position');

        return Db::getInstance()->executeS($sql);
    }

    /**
     * @param $idCms
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getUrlRewriteInformations($idCms)
    {
        $sql = 'SELECT l.`id_lang`, c.`link_rewrite`
				FROM `' . _DB_PREFIX_ . 'cms_lang` AS c
				LEFT JOIN  `' . _DB_PREFIX_ . 'lang` AS l ON c.`id_lang` = l.`id_lang`
				WHERE c.`id_cms` = ' . (int) $idCms . '
				AND l.`active` = 1';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * @param int $idCms
     * @param int|null $idLang
     * @param int|null $idShop
     *
     * @return array|bool|null|object
     */
    public static function getCMSContent($idCms, $idLang = null, $idShop = null)
    {
        if (is_null($idLang)) {
            $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        }
        if (is_null($idShop)) {
            $idShop = (int) Configuration::get('PS_SHOP_DEFAULT');
        }

        $sql = '
			SELECT `content`
			FROM `' . _DB_PREFIX_ . 'cms_lang`
			WHERE `id_cms` = ' . (int) $idCms . ' AND `id_lang` = ' . (int) $idLang . ' AND `id_shop` = ' . (int) $idShop;

        return Db::getInstance()->getRow($sql);
    }

    /**
     * Method required for new PrestaShop Core.
     *
     * @return string
     *
     * @since 1.7.0
     */
    public static function getRepositoryClassName()
    {
        return '\\PrestaShop\\PrestaShop\\Core\\CMS\\CMSRepository';
    }
}
