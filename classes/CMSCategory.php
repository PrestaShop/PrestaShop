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
class CMSCategoryCore extends ObjectModel
{
    public $id;

    /** @var int CMSCategory ID */
    public $id_cms_category;

    /** @var string|array<int, string> Name */
    public $name;

    /** @var bool Status for display */
    public $active = true;

    /** @var string|array<int, string> Description */
    public $description;

    /** @var int Parent CMSCategory ID */
    public $id_parent;

    /** @var int category position */
    public $position;

    /** @var int Parents number */
    public $level_depth;

    /** @var string|array<int, string> string used in rewrited URL */
    public $link_rewrite;

    /** @var string|array<int, string> Meta title */
    public $meta_title;

    /** @var string|array<int, string> Meta keywords */
    public $meta_keywords;

    /** @var string|array<int, string> Meta description */
    public $meta_description;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    protected static $_links = [];

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'cms_category',
        'primary' => 'id_cms_category',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'id_parent' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'position' => ['type' => self::TYPE_INT],
            'level_depth' => ['type' => self::TYPE_INT],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],

            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 64],
            'link_rewrite' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 64],
            'description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'],
            'meta_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'meta_description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 512],
            'meta_keywords' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
        ],
    ];

    public function add($autodate = true, $null_values = false)
    {
        $this->position = CMSCategory::getLastPosition((int) $this->id_parent);
        $this->level_depth = $this->calcLevelDepth();
        foreach ($this->name as $k => $value) {
            if (preg_match('/^[1-9]\./', $value)) {
                $this->name[$k] = '0' . $value;
            }
        }
        $ret = parent::add($autodate, $null_values);
        $this->cleanPositions($this->id_parent);

        return $ret;
    }

    public function update($null_values = false)
    {
        $this->level_depth = $this->calcLevelDepth();
        foreach ($this->name as $k => $value) {
            if (preg_match('/^[1-9]\./', $value)) {
                $this->name[$k] = '0' . $value;
            }
        }

        return parent::update($null_values);
    }

    /**
     * Recursive scan of subcategories.
     *
     * @param int $max_depth Maximum depth of the tree (i.e. 2 => 3 levels depth)
     * @param int $currentDepth specify the current depth in the tree (don't use it, only for rucursivity!)
     * @param int|null $id_lang Specify the id of the language used
     * @param array|null $excluded_ids_array specify a list of ids to exclude of results
     * @param Link|null $link
     *
     * @return array Subcategories lite tree
     */
    public function recurseLiteCategTree($max_depth = 3, $currentDepth = 0, $id_lang = null, $excluded_ids_array = null, Link $link = null)
    {
        if (!$link) {
            $link = Context::getContext()->link;
        }

        if (null === $id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        // recursivity for subcategories
        $children = [];
        $subcats = $this->getSubCategories($id_lang, true);
        if (($max_depth == 0 || $currentDepth < $max_depth) && count($subcats)) {
            foreach ($subcats as $subcat) {
                if (!$subcat['id_cms_category']) {
                    break;
                } elseif (!is_array($excluded_ids_array) || !in_array($subcat['id_cms_category'], $excluded_ids_array)) {
                    $categ = new CMSCategory($subcat['id_cms_category'], $id_lang);
                    $categ->name = CMSCategory::hideCMSCategoryPosition($categ->name);
                    $children[] = $categ->recurseLiteCategTree($max_depth, $currentDepth + 1, $id_lang, $excluded_ids_array);
                }
            }
        }

        return [
            'id' => $this->id_cms_category,
            'link' => $link->getCMSCategoryLink($this->id, $this->link_rewrite),
            'name' => $this->name,
            'desc' => $this->description,
            'children' => $children,
        ];
    }

    public static function getRecurseCategory($id_lang = null, $current = 1, $active = 1, $links = 0, Link $link = null)
    {
        if (!$link) {
            $link = Context::getContext()->link;
        }
        if (null === $id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $sql = 'SELECT c.`id_cms_category`, c.`id_parent`, c.`level_depth`, cl.`name`, cl.`link_rewrite`
				FROM `' . _DB_PREFIX_ . 'cms_category` c
				JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl ON c.`id_cms_category` = cl.`id_cms_category`
					WHERE c.`id_cms_category` = ' . (int) $current . '
					AND `id_lang` = ' . (int) $id_lang;
        /** @var array $category */
        $category = Db::getInstance()->getRow($sql);

        $sql = 'SELECT c.`id_cms_category`
				FROM `' . _DB_PREFIX_ . 'cms_category` c
				' . Shop::addSqlAssociation('cms_category', 'c') . '
				WHERE c.`id_parent` = ' . (int) $current .
                    ($active ? ' AND c.`active` = 1' : '') .
        ' ORDER BY c.`position`';

        $result = Db::getInstance()->executeS($sql);
        foreach ($result as $row) {
            $category['children'][] = CMSCategory::getRecurseCategory($id_lang, $row['id_cms_category'], $active, $links);
        }

        $sql = 'SELECT c.`id_cms`, cl.`meta_title`, cl.`link_rewrite`
				FROM `' . _DB_PREFIX_ . 'cms` c
				' . Shop::addSqlAssociation('cms', 'c') . '
				JOIN `' . _DB_PREFIX_ . 'cms_lang` cl ON c.`id_cms` = cl.`id_cms`
				WHERE `id_cms_category` = ' . (int) $current . ($active ? ' AND c.`active` = 1' : '') . '
				AND cl.`id_shop` = ' . (int) Context::getContext()->shop->id . '
				AND cl.`id_lang` = ' . (int) $id_lang . '
				GROUP BY c.id_cms
				ORDER BY c.`position`';

        $category['cms'] = Db::getInstance()->executeS($sql);
        if ($links == 1) {
            $category['link'] = $link->getCMSCategoryLink($current, $category['link_rewrite']);
            foreach ($category['cms'] as $key => $cms) {
                $category['cms'][$key]['link'] = $link->getCMSLink($cms['id_cms'], $cms['link_rewrite']);
            }
        }

        return $category;
    }

    public static function recurseCMSCategory($categories, $current, $id_cms_category = 1, $id_selected = 1, $is_html = 0)
    {
        $html = '<option value="' . $id_cms_category . '"' . (($id_selected == $id_cms_category) ? ' selected="selected"' : '') . '>'
            . str_repeat('&nbsp;', $current['infos']['level_depth'] * 5)
            . CMSCategory::hideCMSCategoryPosition(stripslashes($current['infos']['name'])) . '</option>';
        if ($is_html == 0) {
            echo $html;
        }
        if (isset($categories[$id_cms_category])) {
            foreach (array_keys($categories[$id_cms_category]) as $key) {
                $html .= CMSCategory::recurseCMSCategory($categories, $categories[$id_cms_category][$key], $key, $id_selected, $is_html);
            }
        }

        return $html;
    }

    /**
     * Recursively add specified CMSCategory childs to $toDelete array.
     *
     * @param array $to_delete Array reference where categories ID will be saved
     * @param array|int $id_cms_category Parent CMSCategory ID
     */
    protected function recursiveDelete(array &$to_delete, $id_cms_category)
    {
        if (!$id_cms_category) {
            die(Tools::displayError('Parameter "id_cms_category" is invalid.'));
        }

        $result = Db::getInstance()->executeS('
		SELECT `id_cms_category`
		FROM `' . _DB_PREFIX_ . 'cms_category`
		WHERE `id_parent` = ' . (int) $id_cms_category);
        foreach ($result as $row) {
            $to_delete[] = (int) $row['id_cms_category'];
            $this->recursiveDelete($to_delete, (int) $row['id_cms_category']);
        }
    }

    /**
     * Directly call the parent of delete, in order to avoid recursion.
     *
     * @return bool Deletion result
     */
    private function deleteLite()
    {
        return parent::delete();
    }

    public function delete()
    {
        if ((int) $this->id === 1) {
            return false;
        }

        $this->clearCache();

        /** @var array<CMSCategory> $cmsCategories */
        $cmsCategories = $this->getAllChildren();
        $cmsCategories[] = $this;
        foreach ($cmsCategories as $cmsCategory) {
            $cmsCategory->deleteCMS();
            $cmsCategory->deleteLite();
            CMSCategory::cleanPositions($cmsCategory->id_parent);
        }

        return true;
    }

    /**
     * Delete pages which are in CMSCategories to delete.
     *
     * @return bool Deletion result
     */
    private function deleteCMS()
    {
        $result = true;
        $cms = new PrestaShopCollection('CMS');
        $cms->where('id_cms_category', '=', $this->id);
        foreach ($cms as $c) {
            $result &= $c->delete();
        }

        return $result;
    }

    /**
     * Delete several categories from database.
     *
     * return boolean Deletion result
     */
    public function deleteSelection(array $categories)
    {
        $return = true;
        foreach ($categories as $id_category_cms) {
            $category_cms = new CMSCategory($id_category_cms);
            $return = $return && $category_cms->delete();
        }

        return $return;
    }

    /**
     * Get the number of parent categories.
     *
     * @return int Level depth
     */
    public function calcLevelDepth()
    {
        $parentCMSCategory = new CMSCategory($this->id_parent);

        return $parentCMSCategory->level_depth + 1;
    }

    /**
     * Return available categories.
     *
     * @param int $id_lang Language ID
     * @param bool $active return only active categories
     *
     * @return array Categories
     */
    public static function getCategories($id_lang, bool $active = true, $order = true)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `' . _DB_PREFIX_ . 'cms_category` c
		LEFT JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl ON c.`id_cms_category` = cl.`id_cms_category`
		WHERE `id_lang` = ' . (int) $id_lang . '
		' . ($active ? 'AND `active` = 1' : '') . '
		ORDER BY `name` ASC');

        if (!$order) {
            return $result;
        }

        $categories = [];
        foreach ($result as $row) {
            $categories[$row['id_parent']][$row['id_cms_category']]['infos'] = $row;
        }

        return $categories;
    }

    public static function getSimpleCategories($id_lang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.`id_cms_category`, cl.`name`
		FROM `' . _DB_PREFIX_ . 'cms_category` c
		LEFT JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl ON (c.`id_cms_category` = cl.`id_cms_category`)
		WHERE cl.`id_lang` = ' . (int) $id_lang . '
		ORDER BY cl.`name`');
    }

    /**
     * Return current CMSCategory childs.
     *
     * @param int $id_lang Language ID
     * @param bool $active return only active categories
     *
     * @return array Categories
     */
    public function getSubCategories(int $id_lang, bool $active = true)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.*, cl.id_lang, cl.name, cl.description, cl.link_rewrite, cl.meta_title, cl.meta_keywords, cl.meta_description
		FROM `' . _DB_PREFIX_ . 'cms_category` c
		LEFT JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl ON (c.`id_cms_category` = cl.`id_cms_category` AND `id_lang` = ' . (int) $id_lang . ')
		WHERE `id_parent` = ' . (int) $this->id . '
		' . ($active ? 'AND `active` = 1' : '') . '
		GROUP BY c.`id_cms_category`
		ORDER BY `position` ASC');

        // Modify SQL result
        foreach ($result as &$row) {
            $row['name'] = CMSCategory::hideCMSCategoryPosition($row['name']);
        }

        return $result;
    }

    /**
     * Hide CMSCategory prefix used for position.
     *
     * @param string $name CMSCategory name
     *
     * @return string Name without position
     */
    public static function hideCMSCategoryPosition($name)
    {
        return preg_replace('/^[0-9]+\./', '', $name);
    }

    /**
     * Return main categories.
     *
     * @param int $id_lang Language ID
     * @param bool $active return only active categories
     *
     * @return array categories
     */
    public static function getHomeCategories($id_lang, $active = true)
    {
        return CMSCategory::getChildren(1, $id_lang, $active);
    }

    public static function getChildren($id_parent, $id_lang, bool $active = true)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.`id_cms_category`, cl.`name`, cl.`link_rewrite`
		FROM `' . _DB_PREFIX_ . 'cms_category` c
		LEFT JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl ON c.`id_cms_category` = cl.`id_cms_category`
		WHERE `id_lang` = ' . (int) $id_lang . '
		AND c.`id_parent` = ' . (int) $id_parent . '
		' . ($active ? 'AND `active` = 1' : '') . '
		ORDER BY `name` ASC');

        // Modify SQL result
        $results_array = [];
        foreach ($result as $row) {
            $row['name'] = CMSCategory::hideCMSCategoryPosition($row['name']);
            $results_array[] = $row;
        }

        return $results_array;
    }

    /**
     * Return an array of all children of the current CMSCategory.
     *
     * @return PrestaShopCollection|array Collection of CMSCategory
     */
    private function getAllChildren()
    {
        // Get children
        $toDelete = [(int) $this->id];
        $this->recursiveDelete($toDelete, (int) $this->id);
        $toDelete = array_unique($toDelete);
        // remove id of current CMSCategory because we want only ids of children
        unset($toDelete[0]);

        if (count($toDelete)) {
            $children = new PrestaShopCollection('CMSCategory');
            $children->where('id_cms_category', 'in', $toDelete);

            return $children;
        }

        return $toDelete;
    }

    /**
     * Check if CMSCategory can be moved in another one.
     *
     * @param int $id_parent Parent candidate
     *
     * @return bool Parent validity
     */
    public static function checkBeforeMove($id_cms_category, $id_parent)
    {
        if ($id_cms_category == $id_parent) {
            return false;
        }
        if ($id_parent == 1) {
            return true;
        }
        $i = (int) $id_parent;

        while (42) {
            $result = Db::getInstance()->getRow('SELECT `id_parent` FROM `' . _DB_PREFIX_ . 'cms_category` WHERE `id_cms_category` = ' . (int) $i);
            if (!isset($result['id_parent'])) {
                return false;
            }
            if ($result['id_parent'] == $id_cms_category) {
                return false;
            }
            if ($result['id_parent'] == 1) {
                return true;
            }
            $i = $result['id_parent'];
        }
    }

    public static function getLinkRewrite($id_cms_category, $id_lang)
    {
        if (!Validate::isUnsignedId($id_cms_category) || !Validate::isUnsignedId($id_lang)) {
            return false;
        }

        if (isset(self::$_links[$id_cms_category . '-' . $id_lang])) {
            return self::$_links[$id_cms_category . '-' . $id_lang];
        }

        $result = Db::getInstance()->getRow('
		SELECT cl.`link_rewrite`
		FROM `' . _DB_PREFIX_ . 'cms_category` c
		LEFT JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl ON c.`id_cms_category` = cl.`id_cms_category`
		WHERE `id_lang` = ' . (int) $id_lang . '
		AND c.`id_cms_category` = ' . (int) $id_cms_category);
        self::$_links[$id_cms_category . '-' . $id_lang] = $result['link_rewrite'];

        return $result['link_rewrite'];
    }

    public function getLink(Link $link = null)
    {
        if (!$link) {
            $link = Context::getContext()->link;
        }

        return $link->getCMSCategoryLink($this->id, $this->link_rewrite);
    }

    public function getName($id_lang = null)
    {
        $context = Context::getContext();
        if (!$id_lang) {
            if (isset($this->name[$context->language->id])) {
                $id_lang = $context->language->id;
            } else {
                $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
            }
        }

        return isset($this->name[$id_lang]) ? $this->name[$id_lang] : '';
    }

    /**
     * Light back office search for categories.
     *
     * @param int $id_lang Language ID
     * @param string $query Searched string
     * @param bool $unrestricted allows search without lang and includes first CMSCategory and exact match
     *
     * @return array Corresponding categories
     */
    public static function searchByName($id_lang, $query, $unrestricted = false)
    {
        if ($unrestricted === true) {
            return Db::getInstance()->getRow('
			SELECT c.*, cl.*
			FROM `' . _DB_PREFIX_ . 'cms_category` c
			LEFT JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl ON (c.`id_cms_category` = cl.`id_cms_category`)
			WHERE `name` = \'' . pSQL($query) . '\'');
        } else {
            return Db::getInstance()->executeS('
			SELECT c.*, cl.*
			FROM `' . _DB_PREFIX_ . 'cms_category` c
			LEFT JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl ON (c.`id_cms_category` = cl.`id_cms_category` AND `id_lang` = ' . (int) $id_lang . ')
			WHERE `name` LIKE \'%' . pSQL($query) . '%\' AND c.`id_cms_category` != 1');
        }
    }

    /**
     * Get Each parent CMSCategory of this CMSCategory until the root CMSCategory.
     *
     * @param int $id_lang Language ID
     *
     * @return array Corresponding categories
     */
    public function getParentsCategories($id_lang = null)
    {
        if (null === $id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $categories = null;
        $id_current = $this->id;
        while (true) {
            $query = '
				SELECT c.*, cl.*
				FROM `' . _DB_PREFIX_ . 'cms_category` c
				LEFT JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl ON (c.`id_cms_category` = cl.`id_cms_category` AND `id_lang` = ' . (int) $id_lang . ')
				WHERE c.`id_cms_category` = ' . (int) $id_current . ' AND c.`id_parent` != 0
			';
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

            $categories[] = $result[0];
            if (!$result || $result[0]['id_parent'] == 1) {
                return $categories;
            }
            $id_current = $result[0]['id_parent'];
        }
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            '
			SELECT cp.`id_cms_category`, cp.`position`, cp.`id_parent`
			FROM `' . _DB_PREFIX_ . 'cms_category` cp
			WHERE cp.`id_parent` = ' . (int) $this->id_parent . '
			ORDER BY cp.`position` ASC'
        )) {
            return false;
        }
        foreach ($res as $category) {
            if ((int) $category['id_cms_category'] == (int) $this->id) {
                $moved_category = $category;
            }
        }

        if (!isset($moved_category) || !isset($position)) {
            return false;
        }
        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'cms_category`
			SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
			WHERE `position`
			' . ($way
                ? '> ' . (int) $moved_category['position'] . ' AND `position` <= ' . (int) $position
                : '< ' . (int) $moved_category['position'] . ' AND `position` >= ' . (int) $position) . '
			AND `id_parent`=' . (int) $moved_category['id_parent'])
        && Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'cms_category`
			SET `position` = ' . (int) $position . '
			WHERE `id_parent` = ' . (int) $moved_category['id_parent'] . '
			AND `id_cms_category`=' . (int) $moved_category['id_cms_category']);
    }

    public static function cleanPositions($id_category_parent)
    {
        $result = Db::getInstance()->executeS('
		SELECT `id_cms_category`
		FROM `' . _DB_PREFIX_ . 'cms_category`
		WHERE `id_parent` = ' . (int) $id_category_parent . '
		ORDER BY `position`');
        $sizeof = count($result);
        for ($i = 0; $i < $sizeof; ++$i) {
            $sql = '
			UPDATE `' . _DB_PREFIX_ . 'cms_category`
			SET `position` = ' . (int) $i . '
			WHERE `id_parent` = ' . (int) $id_category_parent . '
			AND `id_cms_category` = ' . (int) $result[$i]['id_cms_category'];
            Db::getInstance()->execute($sql);
        }

        return true;
    }

    public static function getLastPosition($id_category_parent)
    {
        return Db::getInstance()->getValue('SELECT MAX(position)+1 FROM `' . _DB_PREFIX_ . 'cms_category` WHERE `id_parent` = ' . (int) $id_category_parent);
    }
}
