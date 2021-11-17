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
 * Class CategoryCore.
 */
class CategoryCore extends ObjectModel
{
    public $id;

    /** @var int category ID */
    public $id_category;

    /** @var mixed string or array of Name */
    public $name;

    /** @var bool Status for display */
    public $active = 1;

    /** @var int category position */
    public $position;

    /** @var mixed string or array of Description */
    public $description;

    /** @var int Parent category ID */
    public $id_parent;

    /** @var int default Category id */
    public $id_category_default;

    /** @var int Parents number */
    public $level_depth;

    /** @var int Nested tree model "left" value */
    public $nleft;

    /** @var int Nested tree model "right" value */
    public $nright;

    /** @var mixed string or array of string used in rewrited URL */
    public $link_rewrite;

    /** @var mixed string or array of Meta title */
    public $meta_title;

    /** @var mixed string or array of Meta keywords */
    public $meta_keywords;

    /** @var mixed string or array of Meta description */
    public $meta_description;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /** @var bool is Category Root */
    public $is_root_category;

    /** @var int */
    public $id_shop_default;

    public $groupBox;

    /** @var bool */
    public $doNotRegenerateNTree = false;

    protected static $_links = [];

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'category',
        'primary' => 'id_category',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'nleft' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'nright' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'level_depth' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'id_parent' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_shop_default' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'is_root_category' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'position' => ['type' => self::TYPE_INT],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128],
            'link_rewrite' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isLinkRewrite',
                'required' => true,
                'size' => 128,
                'ws_modifier' => [
                    'http_method' => WebserviceRequest::HTTP_POST,
                    'modifier' => 'modifierWsLinkRewrite',
                ],
            ],
            'description' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
            'meta_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'meta_description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 512],
            'meta_keywords' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
        ],
    ];

    /** @var string id_image is the category ID when an image exists and 'default' otherwise */
    public $id_image = 'default';

    protected $webserviceParameters = [
        'objectsNodeName' => 'categories',
        'hidden_fields' => ['nleft', 'nright', 'groupBox'],
        'fields' => [
            'id_parent' => ['xlink_resource' => 'categories'],
            'level_depth' => ['setter' => false],
            'nb_products_recursive' => ['getter' => 'getWsNbProductsRecursive', 'setter' => false],
        ],
        'associations' => [
            'categories' => ['getter' => 'getChildrenWs', 'resource' => 'category'],
            'products' => ['getter' => 'getProductsWs', 'resource' => 'product'],
        ],
    ];

    /**
     * CategoryCore constructor.
     *
     * @param int|null $idCategory
     * @param int|null $idLang
     * @param int|null $idShop
     */
    public function __construct($idCategory = null, $idLang = null, $idShop = null)
    {
        parent::__construct($idCategory, $idLang, $idShop);
        $this->image_dir = _PS_CAT_IMG_DIR_;
        $this->id_image = ($this->id && file_exists($this->image_dir . (int) $this->id . '.jpg')) ? (int) $this->id : false;
        if (defined('PS_INSTALLATION_IN_PROGRESS')) {
            $this->doNotRegenerateNTree = true;
        }
    }

    /**
     * Get the clean description without HTML tags and slashes.
     *
     * @param string $description Category description with HTML
     *
     * @return string Category description without HTML
     */
    public static function getDescriptionClean($description)
    {
        return Tools::getDescriptionClean($description);
    }

    /**
     * Adds current Category as a new Object to the database.
     *
     * @param bool $autoDate Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Category has been successfully added
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = false)
    {
        if (!isset($this->level_depth)) {
            $this->level_depth = $this->calcLevelDepth();
        }

        if ($this->is_root_category && ($idRootCategory = (int) Configuration::get('PS_ROOT_CATEGORY'))) {
            $this->id_parent = $idRootCategory;
        }

        $ret = parent::add($autoDate, $nullValues);
        if (Tools::isSubmit('checkBoxShopAsso_category')) {
            foreach (Tools::getValue('checkBoxShopAsso_category') as $idShop => $value) {
                $position = (int) Category::getLastPosition((int) $this->id_parent, $idShop);
                $this->addPosition($position, $idShop);
            }
        } else {
            foreach (Shop::getShops(true) as $shop) {
                $position = (int) Category::getLastPosition((int) $this->id_parent, $shop['id_shop']);
                $this->addPosition($position, $shop['id_shop']);
            }
        }

        if (!$this->doNotRegenerateNTree) {
            Category::regenerateEntireNtree();
        }
        // if access group is not set, initialize it with 3 default groups
        $this->updateGroup(($this->groupBox !== null) ? $this->groupBox : []);
        Hook::exec('actionCategoryAdd', ['category' => $this]);

        return $ret;
    }

    /**
     * Updates the current object in the database.
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the CartRule has been successfully updated
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        if ($this->id_parent == $this->id) {
            throw new PrestaShopException('a category cannot be its own parent');
        }

        if ($this->is_root_category && $this->id_parent != (int) Configuration::get('PS_ROOT_CATEGORY')) {
            $this->is_root_category = 0;
        }

        // Update group selection
        $this->updateGroup($this->groupBox);

        if ($this->level_depth != $this->calcLevelDepth()) {
            $this->level_depth = $this->calcLevelDepth();
            $changed = true;
        }

        // If the parent category was changed, we don't want to have 2 categories with the same position
        if (!isset($changed)) {
            $changed = $this->getDuplicatePosition();
        }

        if ($changed) {
            if (Tools::isSubmit('checkBoxShopAsso_category')) {
                foreach (Tools::getValue('checkBoxShopAsso_category') as $idAssoObject => $idShop) {
                    $this->addPosition($this->position, (int) $idShop);
                }
            } else {
                foreach (Shop::getShops(true) as $shop) {
                    $this->addPosition($this->position, $shop['id_shop']);
                }
            }
        }

        $ret = parent::update($nullValues);
        if ($changed && !$this->doNotRegenerateNTree) {
            $this->cleanPositions((int) $this->id_parent);
            Category::regenerateEntireNtree();
            $this->recalculateLevelDepth($this->id);
        }
        Hook::exec('actionCategoryUpdate', ['category' => $this]);

        return $ret;
    }

    /**
     * Toggles the `active` flag.
     *
     * @return bool Indicates whether the status was successfully toggled
     */
    public function toggleStatus()
    {
        $result = parent::toggleStatus();
        Hook::exec('actionCategoryUpdate', ['category' => $this]);

        return $result;
    }

    /**
     * Recursive scan of subcategories.
     *
     * @param int $maxDepth Maximum depth of the tree (i.e. 2 => 3 levels depth)
     * @param int $currentDepth specify the current depth in the tree (don't use it, only for recursive calls!)
     * @param int $idLang Specify the id of the language used
     * @param array $excludedIdsArray Specify a list of IDs to exclude of results
     * @param string $format
     *
     * @return array Subcategories lite tree
     */
    public function recurseLiteCategTree($maxDepth = 3, $currentDepth = 0, $idLang = null, $excludedIdsArray = null, $format = 'default')
    {
        $idLang = null === $idLang ? Context::getContext()->language->id : (int) $idLang;

        $children = [];
        $subcats = $this->getSubCategories($idLang, true);
        if (($maxDepth == 0 || $currentDepth < $maxDepth) && $subcats && count($subcats)) {
            foreach ($subcats as $subcat) {
                if (!$subcat['id_category']) {
                    break;
                } elseif (!is_array($excludedIdsArray) || !in_array($subcat['id_category'], $excludedIdsArray)) {
                    $categ = new Category($subcat['id_category'], $idLang);
                    $children[] = $categ->recurseLiteCategTree($maxDepth, $currentDepth + 1, $idLang, $excludedIdsArray, $format);
                }
            }
        }

        if (is_array($this->description)) {
            foreach ($this->description as $lang => $description) {
                $this->description[$lang] = Category::getDescriptionClean($description);
            }
        } else {
            $this->description = Category::getDescriptionClean($this->description);
        }

        if ($format === 'sitemap') {
            return [
                'id' => 'category-page-' . (int) $this->id,
                'label' => $this->name,
                'url' => Context::getContext()->link->getCategoryLink($this->id, $this->link_rewrite),
                'children' => $children,
            ];
        }

        return [
            'id' => (int) $this->id,
            'link' => Context::getContext()->link->getCategoryLink($this->id, $this->link_rewrite),
            'name' => $this->name,
            'desc' => $this->description,
            'children' => $children,
        ];
    }

    /**
     * Recursively add specified category childs to $to_delete array.
     *
     * @param array &$toDelete Array reference where categories ID will be saved
     * @param int $idCategory Parent category ID
     */
    protected function recursiveDelete(&$toDelete, $idCategory)
    {
        if (!is_array($toDelete) || !$idCategory) {
            die(Tools::displayError());
        }

        $sql = new DbQuery();
        $sql->select('`id_category`');
        $sql->from('category');
        $sql->where('`id_parent` = ' . (int) $idCategory);

        $result = Db::getInstance()->executeS($sql);
        foreach ($result as $row) {
            $toDelete[] = (int) $row['id_category'];
            $this->recursiveDelete($toDelete, (int) $row['id_category']);
        }
    }

    /**
     * Delete this object
     * Skips the deletion procedure of Category and directly calls
     * the delete() method of ObjectModel instead.
     *
     * @return bool Indicates whether this Category was successfully deleted
     */
    public function deleteLite()
    {
        return parent::delete();
    }

    /**
     * Deletes current CartRule from the database.
     *
     * @return bool `true` if successfully deleted
     *
     * @throws PrestaShopException
     */
    public function delete()
    {
        if ((int) $this->id === 0 || (int) $this->id === (int) Configuration::get('PS_ROOT_CATEGORY')) {
            return false;
        }

        $this->clearCache();

        $deletedChildren = $allCat = $this->getAllChildren();
        $allCat[] = $this;
        foreach ($allCat as $cat) {
            /* @var Category $cat */
            $cat->deleteLite();
            if (!$cat->hasMultishopEntries()) {
                $cat->deleteImage();
                $cat->cleanGroups();
                $cat->cleanAssoProducts();
                // Delete associated restrictions on cart rules
                CartRule::cleanProductRuleIntegrity('categories', [$cat->id]);
                Category::cleanPositions($cat->id_parent);
                /* Delete Categories in GroupReduction */
                if (GroupReduction::getGroupsReductionByCategoryId((int) $cat->id)) {
                    GroupReduction::deleteCategory($cat->id);
                }
            }
        }

        /* Rebuild the nested tree */
        if (!$this->hasMultishopEntries() && !$this->doNotRegenerateNTree) {
            Category::regenerateEntireNtree();
        }

        Hook::exec('actionCategoryDelete', ['category' => $this, 'deleted_children' => $deletedChildren]);

        return true;
    }

    /**
     * Delete selected categories from database.
     *
     * @param array $idCategories Category IDs to delete
     *
     * @return bool Deletion result
     */
    public function deleteSelection($idCategories)
    {
        $return = 1;
        foreach ($idCategories as $idCategory) {
            $category = new Category($idCategory);
            if ($category->isRootCategoryForAShop()) {
                return false;
            } else {
                $return &= $category->delete();
            }
        }

        return $return;
    }

    /**
     * Get the depth level for the category.
     *
     * @return int Depth level
     *
     * @throws PrestaShopException
     */
    public function calcLevelDepth()
    {
        /* Root category */
        if (!$this->id_parent) {
            return 0;
        }

        $parentCategory = new Category((int) $this->id_parent);
        if (!Validate::isLoadedObject($parentCategory)) {
            if (is_array($this->name)) {
                $name = $this->name[Context::getContext()->language->id];
            } else {
                $name = $this->name;
            }

            throw new PrestaShopException('Parent category ' . $this->id_parent . ' does not exist. Current category: ' . $name);
        }

        return (int) $parentCategory->level_depth + 1;
    }

    /**
     * Re-calculate the values of all branches of the nested tree.
     */
    public static function regenerateEntireNtree()
    {
        $id = Context::getContext()->shop->id;
        $idShop = $id ? $id : Configuration::get('PS_SHOP_DEFAULT');
        $sql = new DbQuery();
        $sql->select('c.`id_category`, c.`id_parent`');
        $sql->from('category', 'c');
        $sql->leftJoin('category_shop', 'cs', 'c.`id_category` = cs.`id_category` AND cs.`id_shop` = ' . (int) $idShop);
        $sql->orderBy('c.`id_parent`, cs.`position` ASC');
        $categories = Db::getInstance()->executeS($sql);
        $categoriesArray = [];
        foreach ($categories as $category) {
            $categoriesArray[$category['id_parent']]['subcategories'][] = $category['id_category'];
        }
        $n = 1;

        if (isset($categoriesArray[0]) && $categoriesArray[0]['subcategories']) {
            $queries = Category::computeNTreeInfos($categoriesArray, $categoriesArray[0]['subcategories'][0], $n);

            // update by batch of 5000 categories
            $chunks = array_chunk($queries, 5000);
            foreach ($chunks as $chunk) {
                $sqlChunk = array_map(function ($value) { return '(' . rtrim(implode(',', $value)) . ')'; }, $chunk);
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'category` (id_category, nleft, nright)
                VALUES ' . rtrim(implode(',', $sqlChunk), ',') . '
                ON DUPLICATE KEY UPDATE nleft=VALUES(nleft), nright=VALUES(nright)');
            }
        }
    }

    /**
     * @param $categories
     * @param $idCategory
     * @param $n
     *
     * @deprecated 1.7.0
     */
    protected static function _subTree(&$categories, $idCategory, &$n)
    {
        self::subTree($categories, $idCategory, $n);
    }

    /**
     * @param array $categories
     * @param int $idCategory
     * @param int $n
     *
     * @return array ntree infos
     */
    protected static function computeNTreeInfos(&$categories, $idCategory, &$n)
    {
        $queries = [];
        $left = $n++;
        if (isset($categories[(int) $idCategory]['subcategories'])) {
            foreach ($categories[(int) $idCategory]['subcategories'] as $idSubcategory) {
                $queries = array_merge($queries, Category::computeNTreeInfos($categories, (int) $idSubcategory, $n));
            }
        }
        $right = (int) $n++;

        $queries[] = [$idCategory, $left, $right];

        return $queries;
    }

    /**
     * @param $categories
     * @param $idCategory
     * @param $n
     *
     * @return bool Indicates whether the sub tree of categories has been successfully updated
     *
     * @deprecated 1.7.6.0 use computeNTreeInfos + sql query instead
     */
    protected static function subTree(&$categories, $idCategory, &$n)
    {
        $left = $n++;
        if (isset($categories[(int) $idCategory]['subcategories'])) {
            foreach ($categories[(int) $idCategory]['subcategories'] as $idSubcategory) {
                Category::subTree($categories, (int) $idSubcategory, $n);
            }
        }
        $right = (int) $n++;

        return Db::getInstance()->update(
            'category',
            [
                'nleft' => (int) $left,
                'nright' => (int) $right,
            ],
            '`id_category` = ' . (int) $idCategory,
            1
        );
    }

    /**
     * Updates `level_depth` for all children of the given `id_category`.
     *
     * @param int $idParentCategory Parent Category ID
     *
     * @throws PrestaShopException
     */
    public function recalculateLevelDepth($idParentCategory)
    {
        if (!is_numeric($idParentCategory)) {
            throw new PrestaShopException('id category is not numeric');
        }
        /* Gets all children */
        $sql = new DbQuery();
        $sql->select('c.`id_category`, c.`id_parent`, c.`level_depth`');
        $sql->from('category', 'c');
        $sql->where('c.`id_parent` = ' . (int) $idParentCategory);
        $categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        /* Gets level_depth */
        $sql = new DbQuery();
        $sql->select('c.`level_depth`');
        $sql->from('category', 'c');
        $sql->where('c.`id_category` = ' . (int) $idParentCategory);
        $level = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        /* Updates level_depth for all children */
        foreach ($categories as $subCategory) {
            Db::getInstance()->update(
                'category',
                [
                    'level_depth' => (int) ($level['level_depth'] + 1),
                ],
                '`id_category` = ' . (int) $subCategory['id_category']
            );
            /* Recursive call */
            $this->recalculateLevelDepth($subCategory['id_category']);
        }
    }

    /**
     * Return available categories.
     *
     * @param bool|int $idLang Language ID
     * @param bool $active Only return active categories
     * @param bool $order Order the results
     * @param string $sqlFilter Additional SQL clause(s) to filter results
     * @param string $orderBy Change the default order by
     * @param string $limit Set the limit
     *                      Both the offset and limit can be given
     *
     * @return array Categories
     */
    public static function getCategories($idLang = false, $active = true, $order = true, $sqlFilter = '', $orderBy = '', $limit = '')
    {
        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
			SELECT *
			FROM `' . _DB_PREFIX_ . 'category` c
			' . Shop::addSqlAssociation('category', 'c') . '
			LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . '
			WHERE 1 ' . $sqlFilter . ' ' . ($idLang ? 'AND `id_lang` = ' . (int) $idLang : '') . '
			' . ($active ? 'AND `active` = 1' : '') . '
			' . (!$idLang ? 'GROUP BY c.id_category' : '') . '
			' . ($orderBy != '' ? $orderBy : 'ORDER BY c.`level_depth` ASC, category_shop.`position` ASC') . '
			' . ($limit != '' ? $limit : '')
        );

        if (!$order) {
            return $result;
        }

        $categories = [];
        foreach ($result as $row) {
            $categories[$row['id_parent']][$row['id_category']]['infos'] = $row;
        }

        return $categories;
    }

    /**
     * @param int $idRootCategory ID of root Category
     * @param int|bool $idLang Language ID
     *                         `false` if language filter should not be applied
     * @param bool $active Only return active categories
     * @param array|null $groups
     * @param bool $useShopRestriction Restrict to current Shop
     * @param string $sqlFilter Additional SQL clause(s) to filter results
     * @param string $orderBy Change the default order by
     * @param string $limit Set the limit
     *                      Both the offset and limit can be given
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null Array with `id_category` and `name`
     */
    public static function getAllCategoriesName(
        $idRootCategory = null,
        $idLang = false,
        $active = true,
        $groups = null,
        $useShopRestriction = true,
        $sqlFilter = '',
        $orderBy = '',
        $limit = ''
    ) {
        if (isset($idRootCategory) && !Validate::isInt($idRootCategory)) {
            die(Tools::displayError());
        }

        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
            $groups = (array) $groups;
        }

        $cacheId = 'Category::getAllCategoriesName_' . md5(
            (int) $idRootCategory .
            (int) $idLang .
            (int) $active .
            (int) $useShopRestriction .
            (isset($groups) && Group::isFeatureActive() ? implode('', $groups) : '') .
            (isset($sqlFilter) ? $sqlFilter : '') .
            (isset($orderBy) ? $orderBy : '') .
            (isset($limit) ? $limit : '')
        );

        if (!Cache::isStored($cacheId)) {
            $result = Db::getInstance()->executeS(
                '
				SELECT c.`id_category`, cl.`name`
				FROM `' . _DB_PREFIX_ . 'category` c
				' . ($useShopRestriction ? Shop::addSqlAssociation('category', 'c') : '') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . '
				' . (isset($groups) && Group::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cg ON c.`id_category` = cg.`id_category`' : '') . '
				' . (isset($idRootCategory) ? 'RIGHT JOIN `' . _DB_PREFIX_ . 'category` c2 ON c2.`id_category` = ' . (int) $idRootCategory . ' AND c.`nleft` >= c2.`nleft` AND c.`nright` <= c2.`nright`' : '') . '
				WHERE 1 ' . $sqlFilter . ' ' . ($idLang ? 'AND `id_lang` = ' . (int) $idLang : '') . '
				' . ($active ? ' AND c.`active` = 1' : '') . '
				' . (isset($groups) && Group::isFeatureActive() ? ' AND cg.`id_group` IN (' . implode(',', array_map('intval', $groups)) . ')' : '') . '
				' . (!$idLang || (isset($groups) && Group::isFeatureActive()) ? ' GROUP BY c.`id_category`' : '') . '
				' . ($orderBy != '' ? $orderBy : ' ORDER BY c.`level_depth` ASC') . '
				' . ($orderBy == '' && $useShopRestriction ? ', category_shop.`position` ASC' : '') . '
				' . ($limit != '' ? $limit : '')
            );

            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }

        return $result;
    }

    /**
     * Get nested categories.
     *
     * @param int|null $idRootCategory Root Category ID
     * @param int|bool $idLang Language ID
     *                         `false` if language filter should not be used
     * @param bool $active Whether the category must be active
     * @param null $groups
     * @param bool $useShopRestriction Restrict to current Shop
     * @param string $sqlFilter Additional SQL clause(s) to filter results
     * @param string $orderBy Change the default order by
     * @param string $limit Set the limit
     *                      Both the offset and limit can be given
     *
     * @return array|null
     */
    public static function getNestedCategories(
        $idRootCategory = null,
        $idLang = false,
        $active = true,
        $groups = null,
        $useShopRestriction = true,
        $sqlFilter = '',
        $orderBy = '',
        $limit = ''
    ) {
        if (isset($idRootCategory) && !Validate::isInt($idRootCategory)) {
            die(Tools::displayError());
        }

        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
            $groups = (array) $groups;
        }

        $cacheId = 'Category::getNestedCategories_' . md5(
                (int) $idRootCategory .
                (int) $idLang .
                (int) $active .
                (int) $useShopRestriction .
                (isset($groups) && Group::isFeatureActive() ? implode('', $groups) : '') .
                (isset($sqlFilter) ? $sqlFilter : '') .
                (isset($orderBy) ? $orderBy : '') .
                (isset($limit) ? $limit : '')
            );

        if (!Cache::isStored($cacheId)) {
            $result = Db::getInstance()->executeS(
                '
				SELECT c.*, cl.*
				FROM `' . _DB_PREFIX_ . 'category` c
				' . ($useShopRestriction ? Shop::addSqlAssociation('category', 'c') : '') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . '
				' . (isset($groups) && Group::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cg ON c.`id_category` = cg.`id_category`' : '') . '
				' . (isset($idRootCategory) ? 'RIGHT JOIN `' . _DB_PREFIX_ . 'category` c2 ON c2.`id_category` = ' . (int) $idRootCategory . ' AND c.`nleft` >= c2.`nleft` AND c.`nright` <= c2.`nright`' : '') . '
				WHERE 1 ' . $sqlFilter . ' ' . ($idLang ? 'AND `id_lang` = ' . (int) $idLang : '') . '
				' . ($active ? ' AND c.`active` = 1' : '') . '
				' . (isset($groups) && Group::isFeatureActive() ? ' AND cg.`id_group` IN (' . implode(',', array_map('intval', $groups)) . ')' : '') . '
				' . (!$idLang || (isset($groups) && Group::isFeatureActive()) ? ' GROUP BY c.`id_category`' : '') . '
				' . ($orderBy != '' ? $orderBy : ' ORDER BY c.`level_depth` ASC') . '
				' . ($orderBy == '' && $useShopRestriction ? ', category_shop.`position` ASC' : '') . '
				' . ($limit != '' ? $limit : '')
            );

            $categories = [];
            $buff = [];

            if (!isset($idRootCategory)) {
                $idRootCategory = Category::getRootCategory()->id;
            }

            foreach ($result as $row) {
                $current = &$buff[$row['id_category']];
                $current = $row;

                if ($row['id_category'] == $idRootCategory) {
                    $categories[$row['id_category']] = &$current;
                } else {
                    $buff[$row['id_parent']]['children'][$row['id_category']] = &$current;
                }
            }

            Cache::store($cacheId, $categories);
        } else {
            $categories = Cache::retrieve($cacheId);
        }

        return $categories;
    }

    /**
     * Get a simple list of categories with id_category and name for each Category.
     *
     * @param int $idLang Language ID
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public static function getSimpleCategories($idLang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.`id_category`, cl.`name`
		FROM `' . _DB_PREFIX_ . 'category` c
		LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . ')
		' . Shop::addSqlAssociation('category', 'c') . '
		WHERE cl.`id_lang` = ' . (int) $idLang . '
		AND c.`id_category` != ' . Configuration::get('PS_ROOT_CATEGORY') . '
		GROUP BY c.id_category
		ORDER BY c.`id_category`, category_shop.`position`', true, false);
    }

    /**
     * Get a simple list of categories with id_category, name and id_parent infos
     * It also takes into account the root category of the current shop.
     *
     * @param int $idLang Language ID
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public static function getSimpleCategoriesWithParentInfos($idLang)
    {
        $context = Context::getContext();
        if (count(Category::getCategoriesWithoutParent()) > 1
            && \Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')
            && count(Shop::getShops(true, null, true)) !== 1) {
            $idCategoryRoot = (int) \Configuration::get('PS_ROOT_CATEGORY');
        } elseif (!$context->shop->id) {
            $idCategoryRoot = (new Shop(\Configuration::get('PS_SHOP_DEFAULT')))->id_category;
        } else {
            $idCategoryRoot = $context->shop->id_category;
        }

        $rootTreeInfo = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT c.`nleft`, c.`nright` FROM `' . _DB_PREFIX_ . 'category` c ' .
            'WHERE c.`id_category` = ' . (int) $idCategoryRoot
        );

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.`id_category`, cl.`name`, c.id_parent
		FROM `' . _DB_PREFIX_ . 'category` c
		LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
		ON (c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . ')
		' . Shop::addSqlAssociation('category', 'c') . '
		WHERE cl.`id_lang` = ' . (int) $idLang . '
        AND c.`nleft` >= ' . (int) $rootTreeInfo['nleft'] . '
        AND c.`nright` <= ' . (int) $rootTreeInfo['nright'] . '
		GROUP BY c.id_category
		ORDER BY c.`id_category`, category_shop.`position`');
    }

    /**
     * Get Shop ID.
     *
     * @return int
     *
     * @deprecated 1.7.0
     */
    public function getShopID()
    {
        return $this->id_shop;
    }

    /**
     * Return current category childs.
     *
     * @param int $idLang Language ID
     * @param bool $active return only active categories
     *
     * @return array Categories
     */
    public function getSubCategories($idLang, $active = true)
    {
        $sqlGroupsWhere = '';
        $sqlGroupsJoin = '';
        if (Group::isFeatureActive()) {
            $sqlGroupsJoin = 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cg.`id_category` = c.`id_category`)';
            $groups = FrontController::getCurrentCustomerGroups();
            $sqlGroupsWhere = 'AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Configuration::get('PS_UNIDENTIFIED_GROUP'));
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.*, cl.`id_lang`, cl.`name`, cl.`description`, cl.`link_rewrite`, cl.`meta_title`, cl.`meta_keywords`, cl.`meta_description`
		FROM `' . _DB_PREFIX_ . 'category` c
		' . Shop::addSqlAssociation('category', 'c') . '
		LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = ' . (int) $idLang . ' ' . Shop::addSqlRestrictionOnLang('cl') . ')
		' . $sqlGroupsJoin . '
		WHERE `id_parent` = ' . (int) $this->id . '
		' . ($active ? 'AND `active` = 1' : '') . '
		' . $sqlGroupsWhere . '
		GROUP BY c.`id_category`
		ORDER BY `level_depth` ASC, category_shop.`position` ASC');

        foreach ($result as &$row) {
            $row['id_image'] = Tools::file_exists_cache($this->image_dir . $row['id_category'] . '.jpg') ? (int) $row['id_category'] : Language::getIsoById($idLang) . '-default';
            $row['legend'] = 'no picture';
        }

        return $result;
    }

    /**
     * Returns category products.
     *
     * @param int $idLang Language ID
     * @param int $pageNumber Page number
     * @param int $productPerPage Number of products per page
     * @param string|null $orderBy ORDER BY column
     * @param string|null $orderWay Order way
     * @param bool $getTotal If set to true, returns the total number of results only
     * @param bool $active If set to true, finds only active products
     * @param bool $random If true, sets a random filter for returned products
     * @param int $randomNumberProducts Number of products to return if random is activated
     * @param bool $checkAccess If set to `true`, check if the current customer
     *                          can see the products from this category
     * @param Context|null $context Instance of Context
     *
     * @return array|int|false Products, number of products or false (no access)
     *
     * @throws PrestaShopDatabaseException
     */
    public function getProducts(
        $idLang,
        $pageNumber,
        $productPerPage,
        $orderBy = null,
        $orderWay = null,
        $getTotal = false,
        $active = true,
        $random = false,
        $randomNumberProducts = 1,
        $checkAccess = true,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        if ($checkAccess && !$this->checkAccess($context->customer->id)) {
            return false;
        }

        $front = in_array($context->controller->controller_type, ['front', 'modulefront']);
        $idSupplier = (int) Tools::getValue('id_supplier');

        /* Return only the number of products */
        if ($getTotal) {
            $sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `' . _DB_PREFIX_ . 'product` p
					' . Shop::addSqlAssociation('product', 'p') . '
					LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` = ' . (int) $this->id .
                ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') .
                ($active ? ' AND product_shop.`active` = 1' : '') .
                ($idSupplier ? ' AND p.id_supplier = ' . (int) $idSupplier : '');

            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }

        if ($pageNumber < 1) {
            $pageNumber = 1;
        }

        /** Tools::strtolower is a fix for all modules which are now using lowercase values for 'orderBy' parameter */
        $orderBy = Validate::isOrderBy($orderBy) ? Tools::strtolower($orderBy) : 'position';
        $orderWay = Validate::isOrderWay($orderWay) ? Tools::strtoupper($orderWay) : 'ASC';

        $orderByPrefix = false;
        if ($orderBy === 'id_product' || $orderBy === 'date_add' || $orderBy === 'date_upd') {
            $orderByPrefix = 'p';
        } elseif ($orderBy === 'name') {
            $orderByPrefix = 'pl';
        } elseif ($orderBy === 'manufacturer' || $orderBy === 'manufacturer_name') {
            $orderByPrefix = 'm';
            $orderBy = 'name';
        } elseif ($orderBy === 'position') {
            $orderByPrefix = 'cp';
        }

        if ($orderBy === 'price') {
            $orderBy = 'orderprice';
        }

        $nbDaysNewProduct = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nbDaysNewProduct)) {
            $nbDaysNewProduct = 20;
        }

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity' . (Combination::isFeatureActive() ? ', IFNULL(product_attribute_shop.id_product_attribute, 0) AS id_product_attribute,
					product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '') . ', pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image` id_image,
					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
					INTERVAL ' . (int) $nbDaysNewProduct . ' DAY)) > 0 AS new, product_shop.price AS orderprice
				FROM `' . _DB_PREFIX_ . 'category_product` cp
				LEFT JOIN `' . _DB_PREFIX_ . 'product` p
					ON p.`id_product` = cp.`id_product`
				' . Shop::addSqlAssociation('product', 'p') .
                (Combination::isFeatureActive() ? ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')' : '') . '
				' . Product::sqlStock('p', 0) . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('cl') . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('pl') . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = ' . (int) $idLang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE product_shop.`id_shop` = ' . (int) $context->shop->id . '
					AND cp.`id_category` = ' . (int) $this->id
                    . ($active ? ' AND product_shop.`active` = 1' : '')
                    . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
                    . ($idSupplier ? ' AND p.id_supplier = ' . (int) $idSupplier : '');

        if ($random === true) {
            $sql .= ' ORDER BY RAND() LIMIT ' . (int) $randomNumberProducts;
        } elseif ($orderBy !== 'orderprice') {
            $sql .= ' ORDER BY ' . (!empty($orderByPrefix) ? $orderByPrefix . '.' : '') . '`' . bqSQL($orderBy) . '` ' . pSQL($orderWay) . '
			LIMIT ' . (((int) $pageNumber - 1) * (int) $productPerPage) . ',' . (int) $productPerPage;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

        if (!$result) {
            return [];
        }

        if ($orderBy === 'orderprice') {
            Tools::orderbyPrice($result, $orderWay);
            $result = array_slice($result, (int) (($pageNumber - 1) * $productPerPage), (int) $productPerPage);
        }

        // Modify SQL result
        return Product::getProductsProperties($idLang, $result);
    }

    /**
     * Return main categories.
     *
     * @param int $idLang Language ID
     * @param bool $active return only active categories
     *
     * @return array categories
     */
    public static function getHomeCategories($idLang, $active = true, $idShop = false)
    {
        return self::getChildren(Configuration::get('PS_HOME_CATEGORY'), $idLang, $active, $idShop);
    }

    /**
     * Get root Category object
     * Returns the top Category if there are multiple root Categories.
     *
     * @param int|null $idLang Language ID
     * @param Shop|null $shop Shop object
     *
     * @return Category object
     */
    public static function getRootCategory($idLang = null, Shop $shop = null)
    {
        $context = Context::getContext();
        if (null === $idLang) {
            $idLang = $context->language->id;
        }
        if (!$shop) {
            if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
                $shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
            } else {
                $shop = $context->shop;
            }
        } else {
            return new Category($shop->getCategory(), $idLang);
        }
        $isMoreThanOneRootCategory = count(Category::getCategoriesWithoutParent()) > 1;
        if (Shop::isFeatureActive() && $isMoreThanOneRootCategory) {
            $category = Category::getTopCategory($idLang);
        } else {
            $category = new Category($shop->getCategory(), $idLang);
        }

        return $category;
    }

    /**
     * Get children of the given Category.
     *
     * @param int $idParent Parent Category ID
     * @param int $idLang Language ID
     * @param bool $active Active children only
     * @param bool $idShop Shop ID
     *
     * @return array Children of given Category
     */
    public static function getChildren($idParent, $idLang, $active = true, $idShop = false)
    {
        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        $cacheId = 'Category::getChildren_' . (int) $idParent . '-' . (int) $idLang . '-' . (bool) $active . '-' . (int) $idShop;
        if (!Cache::isStored($cacheId)) {
            $query = 'SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`, category_shop.`id_shop`
			FROM `' . _DB_PREFIX_ . 'category` c
			LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . ')
			' . Shop::addSqlAssociation('category', 'c') . '
			WHERE `id_lang` = ' . (int) $idLang . '
			AND c.`id_parent` = ' . (int) $idParent . '
			' . ($active ? 'AND `active` = 1' : '') . '
			GROUP BY c.`id_category`
			ORDER BY category_shop.`position` ASC';
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Check if the given Category has child categories.
     *
     * @param int $idParent Parent Category ID
     * @param int $idLang Language ID
     * @param bool $active Active children only
     * @param bool $idShop Shop ID
     *
     * @return bool Indicates whether the given Category has children
     */
    public static function hasChildren($idParent, $idLang, $active = true, $idShop = false)
    {
        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        $cacheId = 'Category::hasChildren_' . (int) $idParent . '-' . (int) $idLang . '-' . (bool) $active . '-' . (int) $idShop;
        if (!Cache::isStored($cacheId)) {
            $query = 'SELECT c.id_category, "" as name
			FROM `' . _DB_PREFIX_ . 'category` c
			LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . ')
			' . Shop::addSqlAssociation('category', 'c') . '
			WHERE `id_lang` = ' . (int) $idLang . '
			AND c.`id_parent` = ' . (int) $idParent . '
			' . ($active ? 'AND `active` = 1' : '') . ' LIMIT 1';
            $result = (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Return an array of all children of the current category.
     *
     * @param int $idLang Language ID
     *
     * @return PrestaShopCollection Collection of Category
     */
    public function getAllChildren($idLang = null)
    {
        if (null === $idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $categories = new PrestaShopCollection('Category', $idLang);
        $categories->where('nleft', '>', $this->nleft);
        $categories->where('nright', '<', $this->nright);

        return $categories;
    }

    /**
     * Return an ordered array of all parents of the current category.
     *
     * @param int $idLang
     *
     * @return PrestaShopCollection Collection of Category
     */
    public function getAllParents($idLang = null)
    {
        if (null === $idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $categories = new PrestaShopCollection('Category', $idLang);
        $categories->where('nleft', '<', $this->nleft);
        $categories->where('nright', '>', $this->nright);
        $categories->orderBy('nleft');

        return $categories;
    }

    /**
     * This method allow to return children categories with the number of sub children selected for a product.
     *
     * @param int $idParent Parent Category ID
     * @param int $selectedCategory Selected SubCategory ID
     * @param int $idLang Language ID
     * @param Shop $shop Shop ID
     * @param bool $useShopContext Limit to current Shop
     *
     * @return array
     *
     * @internal param int $id_product Product ID
     */
    public static function getChildrenWithNbSelectedSubCat($idParent, $selectedCategory, $idLang, Shop $shop = null, $useShopContext = true)
    {
        if (!$shop) {
            $shop = Context::getContext()->shop;
        }

        $idShop = $shop->id ? $shop->id : Configuration::get('PS_SHOP_DEFAULT');
        $selectedCategory = explode(',', str_replace(' ', '', $selectedCategory));
        $sql = '
		SELECT c.`id_category`, c.`level_depth`, cl.`name`,
		IF((
			SELECT COUNT(*)
			FROM `' . _DB_PREFIX_ . 'category` c2
			WHERE c2.`id_parent` = c.`id_category`
		) > 0, 1, 0) AS has_children,
		' . ($selectedCategory ? '(
			SELECT count(c3.`id_category`)
			FROM `' . _DB_PREFIX_ . 'category` c3
			WHERE c3.`nleft` > c.`nleft`
			AND c3.`nright` < c.`nright`
			AND c3.`id_category`  IN (' . implode(',', array_map('intval', $selectedCategory)) . ')
		)' : '0') . ' AS nbSelectedSubCat
		FROM `' . _DB_PREFIX_ . 'category` c
		LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category` ' . Shop::addSqlRestrictionOnLang('cl', (int) $idShop) . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = ' . (int) $idShop . ')
		WHERE `id_lang` = ' . (int) $idLang . '
		AND c.`id_parent` = ' . (int) $idParent;
        if (Shop::getContext() === Shop::CONTEXT_SHOP && $useShopContext) {
            $sql .= ' AND cs.`id_shop` = ' . (int) $shop->id;
        }
        if (!Shop::isFeatureActive() || Shop::getContext() === Shop::CONTEXT_SHOP && $useShopContext) {
            $sql .= ' ORDER BY cs.`position` ASC';
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Copy products from a category to another.
     *
     * @param int $idOld Source category ID
     * @param bool $idNew Destination category ID
     *
     * @return bool Duplication result
     */
    public static function duplicateProductCategories($idOld, $idNew)
    {
        $sql = 'SELECT `id_category`
				FROM `' . _DB_PREFIX_ . 'category_product`
				WHERE `id_product` = ' . (int) $idOld;
        $result = Db::getInstance()->executeS($sql);

        $row = [];
        if ($result) {
            foreach ($result as $i) {
                $row[] = '(' . implode(', ', [(int) $idNew, $i['id_category'], '(SELECT tmp.max + 1 FROM (
					SELECT MAX(cp.`position`) AS max
					FROM `' . _DB_PREFIX_ . 'category_product` cp
					WHERE cp.`id_category`=' . (int) $i['id_category'] . ') AS tmp)',
                ]) . ')';
            }
        }

        $flag = Db::getInstance()->execute(
            '
			INSERT IGNORE INTO `' . _DB_PREFIX_ . 'category_product` (`id_product`, `id_category`, `position`)
			VALUES ' . implode(',', $row)
        );

        return $flag;
    }

    /**
     * Check if category can be moved in another one.
     * The category cannot be moved in a child category.
     *
     * @param int $idCategory Current category
     * @param int $idParent Parent candidate
     *
     * @return bool Parent validity
     */
    public static function checkBeforeMove($idCategory, $idParent)
    {
        if ($idCategory == $idParent) {
            return false;
        }
        if ($idParent == Configuration::get('PS_HOME_CATEGORY')) {
            return true;
        }
        $i = (int) $idParent;

        while (42) {
            $result = Db::getInstance()->getRow('SELECT `id_parent` FROM `' . _DB_PREFIX_ . 'category` WHERE `id_category` = ' . (int) $i);
            if (!isset($result['id_parent'])) {
                return false;
            }
            if ($result['id_parent'] == $idCategory) {
                return false;
            }
            if ($result['id_parent'] == Configuration::get('PS_HOME_CATEGORY')) {
                return true;
            }
            $i = $result['id_parent'];
        }

        return false;
    }

    /**
     * Get the rewrite link of the given Category.
     *
     * @param int $idCategory Category ID
     * @param int $idLang Language ID
     *
     * @return bool|mixed
     */
    public static function getLinkRewrite($idCategory, $idLang)
    {
        if (!Validate::isUnsignedId($idCategory) || !Validate::isUnsignedId($idLang)) {
            return false;
        }

        if (!isset(self::$_links[$idCategory . '-' . $idLang])) {
            self::$_links[$idCategory . '-' . $idLang] = Db::getInstance()->getValue('
			SELECT cl.`link_rewrite`
			FROM `' . _DB_PREFIX_ . 'category_lang` cl
			WHERE `id_lang` = ' . (int) $idLang . '
			' . Shop::addSqlRestrictionOnLang('cl') . '
			AND cl.`id_category` = ' . (int) $idCategory);
        }

        return self::$_links[$idCategory . '-' . $idLang];
    }

    /**
     * Get link to this category.
     *
     * @param Link|null $link Link instance
     * @param int|null $idLang Language ID
     *
     * @return string FO URL to this Category
     */
    public function getLink(Link $link = null, $idLang = null)
    {
        if (!$link) {
            $link = Context::getContext()->link;
        }

        if (!$idLang && is_array($this->link_rewrite)) {
            $idLang = Context::getContext()->language->id;
        }

        return $link->getCategoryLink(
            $this,
            is_array($this->link_rewrite) ? $this->link_rewrite[$idLang] : $this->link_rewrite,
            $idLang
        );
    }

    /**
     * Get category name in given Language.
     *
     * @param int|null $idLang Language ID
     *
     * @return string Category name
     */
    public function getName($idLang = null)
    {
        if (!$idLang) {
            if (isset($this->name[Context::getContext()->language->id])) {
                $idLang = Context::getContext()->language->id;
            } else {
                $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
            }
        }

        return isset($this->name[$idLang]) ? $this->name[$idLang] : '';
    }

    /**
     * Light back office search for categories.
     *
     * @param int $idLang Language ID
     * @param string $query Searched string
     * @param bool $unrestricted Allows search without lang and includes first category and exact match
     * @param bool $skipCache Skip the Cache
     *
     * @return array Corresponding categories
     *
     * @throws PrestaShopDatabaseException
     */
    public static function searchByName($idLang, $query, $unrestricted = false, $skipCache = false)
    {
        if ($unrestricted === true) {
            $key = 'Category::searchByName_' . $query;
            if ($skipCache || !Cache::isStored($key)) {
                $sql = new DbQuery();
                $sql->select('c.*, cl.*');
                $sql->from('category', 'c');
                $sql->leftJoin('category_lang', 'cl', 'c.`id_category` = cl.`id_category` ' . Shop::addSqlRestrictionOnLang('cl'));
                $sql->where('`name` = \'' . pSQL($query) . '\'');
                $categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
                if (!$skipCache) {
                    Cache::store($key, $categories);
                }

                return $categories;
            }

            return Cache::retrieve($key);
        } else {
            $sql = new DbQuery();
            $sql->select('c.*, cl.*');
            $sql->from('category', 'c');
            $sql->leftJoin('category_lang', 'cl', 'c.`id_category` = cl.`id_category` AND `id_lang` = ' . (int) $idLang . ' ' . Shop::addSqlRestrictionOnLang('cl'));
            $sql->where('`name` LIKE \'%' . pSQL($query) . '%\'');
            $sql->where('c.`id_category` != ' . (int) Configuration::get('PS_HOME_CATEGORY'));

            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }

    /**
     * Retrieve category by name and parent category id.
     *
     * @param int $idLang Language ID
     * @param string $categoryName Searched category name
     * @param int $idParentCategory parent category ID
     *
     * @return array Corresponding category
     */
    public static function searchByNameAndParentCategoryId($idLang, $categoryName, $idParentCategory)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT c.*, cl.*
		FROM `' . _DB_PREFIX_ . 'category` c
		LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
			ON (c.`id_category` = cl.`id_category`
			AND `id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('cl') . ')
		WHERE `name` = \'' . pSQL($categoryName) . '\'
			AND c.`id_category` != ' . (int) Configuration::get('PS_HOME_CATEGORY') . '
			AND c.`id_parent` = ' . (int) $idParentCategory);
    }

    /**
     * Search with paths for Categories.
     *
     * @param int $idLang Language ID
     * @param string $path Path of category
     * @param bool $objectToCreate a category
     * @param bool $methodToCreate a category
     *
     * @return array Corresponding categories
     */
    public static function searchByPath($idLang, $path, $objectToCreate = false, $methodToCreate = false)
    {
        $categories = explode('/', trim($path));
        $category = $idParentCategory = false;

        if (is_array($categories) && count($categories)) {
            foreach ($categories as $categoryName) {
                if ($idParentCategory) {
                    $category = Category::searchByNameAndParentCategoryId($idLang, $categoryName, $idParentCategory);
                } else {
                    $category = Category::searchByName($idLang, $categoryName, true, true);
                }

                if (!$category && $objectToCreate && $methodToCreate) {
                    call_user_func_array([$objectToCreate, $methodToCreate], [$idLang, $categoryName, $idParentCategory]);
                    $category = Category::searchByPath($idLang, $categoryName);
                }
                if (isset($category['id_category']) && $category['id_category']) {
                    $idParentCategory = (int) $category['id_category'];
                }
            }
        }

        return $category;
    }

    /**
     * Get Each parent category of this category until the root category.
     *
     * @param int $idLang Language ID
     *
     * @return array Corresponding categories
     */
    public function getParentsCategories($idLang = null)
    {
        $context = Context::getContext()->cloneContext();
        $context->shop = clone $context->shop;

        if (null === $idLang) {
            $idLang = $context->language->id;
        }

        $categories = null;
        $idCurrent = $this->id;
        if (!$context->shop->id) {
            $context->shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
        }
        if (count(Category::getCategoriesWithoutParent()) > 1) {
            $context->shop->id_category = (int) Configuration::get('PS_ROOT_CATEGORY');
        }
        $idShop = $context->shop->id;

        $sqlAppend = 'FROM `' . _DB_PREFIX_ . 'category` c
			LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
				ON (c.`id_category` = cl.`id_category`
                    AND `id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('cl') . ')';
        if (Shop::isFeatureActive() && Shop::getContext() === Shop::CONTEXT_SHOP) {
            $sqlAppend .= ' LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs ' .
                'ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = ' . (int) $idShop . ')';
        }
        if (Shop::isFeatureActive() && Shop::getContext() === Shop::CONTEXT_SHOP) {
            $sqlAppend .= ' AND cs.`id_shop` = ' . (int) $context->shop->id;
        }
        $rootCategory = Category::getRootCategory();
        if (Shop::isFeatureActive() && Shop::getContext() === Shop::CONTEXT_SHOP
            && (!Tools::isSubmit('id_category')
                || (int) Tools::getValue('id_category') == (int) $rootCategory->id
                || (int) $rootCategory->id == (int) $context->shop->id_category)) {
            $sqlAppend .= ' AND c.`id_parent` != 0';
        }

        $categories = [];

        $treeInfo = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT c.`nleft`, c.`nright`  ' . $sqlAppend . ' WHERE c.`id_category` = ' . (int) $idCurrent
        );

        if (!empty($treeInfo)) {
            $rootTreeInfo = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
                'SELECT c.`nleft`, c.`nright` FROM `' . _DB_PREFIX_ . 'category` c
            WHERE c.`id_category` = ' . (int) $context->shop->id_category
            );

            $categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT c.*, cl.*  ' . $sqlAppend .
                ' WHERE c.`nleft` <= ' . (int) $treeInfo['nleft'] .
                ' AND c.`nright` >= ' . (int) $treeInfo['nright'] .
                ' AND c.`nleft` >= ' . (int) $rootTreeInfo['nleft'] .
                ' AND c.`nright` <= ' . (int) $rootTreeInfo['nright'] .
                ' ORDER BY `nleft` DESC'
            );
        }

        return $categories;
    }

    /**
     * Specify if a category already in base.
     *
     * @param int $idCategory Category id
     *
     * @return bool
     */
    public static function categoryExists($idCategory)
    {
        $row = Db::getInstance()->getRow('
		SELECT `id_category`
		FROM ' . _DB_PREFIX_ . 'category c
		WHERE c.`id_category` = ' . (int) $idCategory, false);

        return isset($row['id_category']);
    }

    /**
     * Check if all categories by provided ids are present in database.
     * If at least one is missing return false
     *
     * @param int[] $categoryIds
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     */
    public static function categoriesExists(array $categoryIds): bool
    {
        if (empty($categoryIds)) {
            return false;
        }

        $categoryIds = array_map('intval', array_unique($categoryIds, SORT_REGULAR));
        $categoryIdsFormatted = implode(',', $categoryIds);

        $result = Db::getInstance()->query('
            SELECT COUNT(c.id_category) as categories_found
            FROM ' . _DB_PREFIX_ . 'category c
            WHERE c.id_category IN (' . $categoryIdsFormatted . ')
        ')->fetch();

        return count($categoryIds) === (int) $result['categories_found'];
    }

    /**
     * Clean Category Groups.
     *
     * @return bool Indicated whether the cleanup was successful
     */
    public function cleanGroups()
    {
        return Db::getInstance()->delete('category_group', 'id_category = ' . (int) $this->id);
    }

    /**
     * Remove associated products.
     *
     * @return bool Indicates whether the cleanup was successful
     */
    public function cleanAssoProducts()
    {
        return Db::getInstance()->delete('category_product', 'id_category = ' . (int) $this->id);
    }

    /**
     * Add Category groups.
     *
     * @param $groups
     */
    public function addGroups($groups)
    {
        foreach ($groups as $group) {
            if ($group !== false) {
                Db::getInstance()->insert('category_group', ['id_category' => (int) $this->id, 'id_group' => (int) $group]);
            }
        }
    }

    /**
     * Get Category groups.
     *
     * @return array|null
     */
    public function getGroups()
    {
        $cacheId = 'Category::getGroups_' . (int) $this->id;
        if (!Cache::isStored($cacheId)) {
            $sql = new DbQuery();
            $sql->select('cg.`id_group`');
            $sql->from('category_group', 'cg');
            $sql->where('cg.`id_category` = ' . (int) $this->id);
            $result = Db::getInstance()->executeS($sql);
            $groups = [];
            foreach ($result as $group) {
                $groups[] = $group['id_group'];
            }
            Cache::store($cacheId, $groups);

            return $groups;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Add group if it does not exist.
     *
     * @param int $idGroup Group ID
     *
     * @return bool|void
     */
    public function addGroupsIfNoExist($idGroup)
    {
        $groups = $this->getGroups();
        if (!in_array((int) $idGroup, $groups)) {
            return $this->addGroups([(int) $idGroup]);
        }

        return false;
    }

    /**
     * checkAccess return true if id_customer is in a group allowed to see this category.
     *
     * @param mixed $idCustomer
     *
     * @return bool true if access allowed for customer $id_customer
     */
    public function checkAccess($idCustomer)
    {
        $cacheId = 'Category::checkAccess_' . (int) $this->id . '-' . $idCustomer . (!$idCustomer ? '-' . (int) Group::getCurrent()->id : '');
        if (!Cache::isStored($cacheId)) {
            if (!$idCustomer) {
                $result = (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT ctg.`id_group`
				FROM ' . _DB_PREFIX_ . 'category_group ctg
				WHERE ctg.`id_category` = ' . (int) $this->id . ' AND ctg.`id_group` = ' . (int) Group::getCurrent()->id);
            } else {
                $result = (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT ctg.`id_group`
				FROM ' . _DB_PREFIX_ . 'category_group ctg
				INNER JOIN ' . _DB_PREFIX_ . 'customer_group cg on (cg.`id_group` = ctg.`id_group` AND cg.`id_customer` = ' . (int) $idCustomer . ')
				WHERE ctg.`id_category` = ' . (int) $this->id);
            }
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Update customer groups associated to the object. Don't update group access if list is null.
     *
     * @param array $list groups
     *
     * @return bool
     */
    public function updateGroup($list)
    {
        // don't update group access if list is null
        if ($list === null) {
            return false;
        }
        $this->cleanGroups();
        if (empty($list)) {
            $list = [Configuration::get('PS_UNIDENTIFIED_GROUP'), Configuration::get('PS_GUEST_GROUP'), Configuration::get('PS_CUSTOMER_GROUP')];
        }
        $this->addGroups($list);

        return true;
    }

    /**
     * @param $idGroup
     *
     * @return bool
     */
    public static function setNewGroupForHome($idGroup)
    {
        if (!(int) $idGroup) {
            return false;
        }

        return Db::getInstance()->execute('
		INSERT INTO `' . _DB_PREFIX_ . 'category_group` (`id_category`, `id_group`)
		VALUES (' . (int) Context::getContext()->shop->getCategory() . ', ' . (int) $idGroup . ')');
    }

    /**
     * Update the position of the current Category.
     *
     * @param bool $way Indicates whether the Category should move up (`false`) or down (`true`)
     * @param int $position Current Position
     *
     * @return bool
     */
    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
            SELECT cp.`id_category`, category_shop.`position`, cp.`id_parent`
            FROM `' . _DB_PREFIX_ . 'category` cp
            ' . Shop::addSqlAssociation('category', 'cp') . '
            WHERE cp.`id_parent` = ' . (int) $this->id_parent . '
            ORDER BY category_shop.`position` ASC')
            ) {
            return false;
        }

        $movedCategory = false;
        foreach ($res as $category) {
            if ((int) $category['id_category'] == (int) $this->id) {
                $movedCategory = $category;
            }
        }

        if ($movedCategory === false) {
            return false;
        }
        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        $increment = ($way ? '- 1' : '+ 1');
        $result = (Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'category` c ' . Shop::addSqlAssociation('category', 'c') . ' ' .
            'SET c.`position`= ' .
            'IF(cast(c.`position` as signed) ' . $increment . ' > 0, c.`position` ' . $increment . ', 0), ' .
            'category_shop.`position` = ' .
            'IF(cast(category_shop.`position` as signed) ' . $increment . ' > 0, category_shop.`position` ' . $increment . ', 0), ' .
            'c.`date_upd` = "' . date('Y-m-d H:i:s') . '" ' .
            'WHERE category_shop.`position`' .
            ($way
                ? '> ' . (int) $movedCategory['position'] . ' AND category_shop.`position` <= ' . (int) $position
                : '< ' . (int) $movedCategory['position'] . ' AND category_shop.`position` >= ' . (int) $position) . ' ' .
            'AND c.`id_parent`=' . (int) $movedCategory['id_parent'])
        && Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'category` c ' . Shop::addSqlAssociation('category', 'c') . '
            SET c.`position` = ' . (int) $position . ',
            category_shop.`position` = ' . (int) $position . ',
            c.`date_upd` = "' . date('Y-m-d H:i:s') . '"
            WHERE c.`id_parent` = ' . (int) $movedCategory['id_parent'] . '
            AND c.`id_category`=' . (int) $movedCategory['id_category']));
        Hook::exec('actionCategoryUpdate', ['category' => new Category($movedCategory['id_category'])]);

        return $result;
    }

    /**
     * cleanPositions keep order of category in $id_category_parent,
     * but remove duplicate position. Should not be used if positions
     * are clean at the beginning !
     *
     * @param mixed $idCategoryParent
     *
     * @return bool true if succeed
     */
    public static function cleanPositions($idCategoryParent = null)
    {
        if ($idCategoryParent === null) {
            return;
        }

        $return = true;
        $result = Db::getInstance()->executeS('
        SELECT c.`id_category`
        FROM `' . _DB_PREFIX_ . 'category` c
        ' . Shop::addSqlAssociation('category', 'c') . '
        WHERE c.`id_parent` = ' . (int) $idCategoryParent . '
        ORDER BY category_shop.`position`');
        $count = count($result);
        for ($i = 0; $i < $count; ++$i) {
            $return &= Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'category` c ' . Shop::addSqlAssociation('category', 'c') . '
            SET c.`position` = ' . (int) ($i) . ',
            category_shop.`position` = ' . (int) ($i) . ',
            c.`date_upd` = "' . date('Y-m-d H:i:s') . '"
            WHERE c.`id_parent` = ' . (int) $idCategoryParent . ' AND c.`id_category` = ' . (int) $result[$i]['id_category']);
        }

        return $return;
    }

    /**
     * Returns the number of categories + 1 having $idCategoryParent as parent.
     *
     * @param int $idCategoryParent The parent category
     * @param int $idShop Shop ID
     *
     * @return int Number of categories + 1 having $idCategoryParent as parent
     *
     * @todo     rename that function to make it understandable (getNextPosition for example)
     */
    public static function getLastPosition($idCategoryParent, $idShop)
    {
        // @TODO, if we remove this query, the position will begin at 1 instead of 0, but is this really a problem?
        $results = Db::getInstance()->executeS('
				SELECT 1
				FROM `' . _DB_PREFIX_ . 'category` c
				 JOIN `' . _DB_PREFIX_ . 'category_shop` cs
				ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = ' . (int) $idShop . ')
				WHERE c.`id_parent` = ' . (int) $idCategoryParent . ' LIMIT 2');

        if (count($results) === 1) {
            return 0;
        } else {
            $maxPosition = (int) Db::getInstance()->getValue('
				SELECT MAX(cs.`position`)
				FROM `' . _DB_PREFIX_ . 'category` c
				LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs
				ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = ' . (int) $idShop . ')
				WHERE c.`id_parent` = ' . (int) $idCategoryParent);

            return 1 + $maxPosition;
        }
    }

    /**
     * @see self::getUrlRewriteInformation()
     * @deprecated 1.7.0
     */
    public static function getUrlRewriteInformations($idCategory)
    {
        return self::getUrlRewriteInformation($idCategory);
    }

    /**
     * Get URL Rewrite information.
     *
     * @param $idCategory
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     *
     * @since 1.7.0
     */
    public static function getUrlRewriteInformation($idCategory)
    {
        $sql = new DbQuery();
        $sql->select('l.`id_lang`, cl.`link_rewrite`');
        $sql->from('category_link', 'cl');
        $sql->leftJoin('lang', 'l', 'cl.`id_lang` = l.`id_lang`');
        $sql->where('cl.`id_category` = ' . (int) $idCategory);
        $sql->where('l.`active` = 1');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Return `nleft` and `nright` fields for a given category.
     *
     * @param int $id
     *
     * @return array
     *
     * @since 1.5.0
     */
    public static function getInterval($id)
    {
        $cacheId = 'Category::getInterval_' . (int) $id;
        if (!Cache::isStored($cacheId)) {
            $sql = new DbQuery();
            $sql->select('c.`nleft`, c.`nright`, c.`level_depth`');
            $sql->from('category', 'c');
            $sql->where('c.`id_category` = ' . (int) $id);
            $result = Db::getInstance()->getRow($sql);
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Check if current category is a child of shop root category.
     *
     * @param Shop $shop
     *
     * @return bool
     *
     * @since 1.5.0
     */
    public function inShop(Shop $shop = null)
    {
        if (!$shop) {
            $shop = Context::getContext()->shop;
        }

        if (!$interval = Category::getInterval($shop->getCategory())) {
            return false;
        }

        return $this->nleft >= $interval['nleft'] && $this->nright <= $interval['nright'];
    }

    /**
     * Check if current category is a child of shop root category.
     *
     * @param int $idCategory Category ID
     * @param Shop $shop Shop object
     *
     * @return bool Indicates whether the current category is a child of the Shop root category
     *
     * @since 1.5.0
     */
    public static function inShopStatic($idCategory, Shop $shop = null)
    {
        if (!$shop || !is_object($shop)) {
            $shop = Context::getContext()->shop;
        }

        if (!$interval = Category::getInterval($shop->getCategory())) {
            return false;
        }
        $sql = new DbQuery();
        $sql->select('c.`nleft`, c.`nright`');
        $sql->from('category', 'c');
        $sql->where('c.`id_category` = ' . (int) $idCategory);
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        return $row['nleft'] >= $interval['nleft'] && $row['nright'] <= $interval['nright'];
    }

    /**
     * Get Children for the webservice.
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public function getChildrenWs()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.`id_category` as id
		FROM `' . _DB_PREFIX_ . 'category` c
		' . Shop::addSqlAssociation('category', 'c') . '
		WHERE c.`id_parent` = ' . (int) $this->id . '
		AND c.`active` = 1
		ORDER BY category_shop.`position` ASC');
    }

    /**
     * Get Products for webservice.
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public function getProductsWs()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT cp.`id_product` as id
		FROM `' . _DB_PREFIX_ . 'category_product` cp
		WHERE cp.`id_category` = ' . (int) $this->id . '
		ORDER BY `position` ASC');
    }

    /*
        Create the link rewrite if not exists or invalid on category creation
    */
    public function modifierWsLinkRewrite()
    {
        foreach ($this->name as $id_lang => $name) {
            if (empty($this->link_rewrite[$id_lang])) {
                $this->link_rewrite[$id_lang] = Tools::link_rewrite($name);
            } elseif (!Validate::isLinkRewrite($this->link_rewrite[$id_lang])) {
                $this->link_rewrite[$id_lang] = Tools::link_rewrite($this->link_rewrite[$id_lang]);
            }
        }

        return true;
    }

    /**
     * Search for another Category with the same parent and the same position.
     *
     * @return array first Category found
     */
    public function getDuplicatePosition()
    {
        return Db::getInstance()->getValue('
		SELECT c.`id_category`
		FROM `' . _DB_PREFIX_ . 'category` c
		' . Shop::addSqlAssociation('category', 'c') . '
		WHERE c.`id_parent` = ' . (int) $this->id_parent . '
		AND category_shop.`position` = ' . (int) $this->position . '
		AND c.`id_category` != ' . (int) $this->id);
    }

    /**
     * Recursively get amount of Products for the webservice.
     *
     * @return false|int|string|null
     */
    public function getWsNbProductsRecursive()
    {
        $nbProductRecursive = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(distinct(id_product))
			FROM  `' . _DB_PREFIX_ . 'category_product`
			WHERE id_category = ' . (int) $this->id . ' OR
			EXISTS (
				SELECT 1
				FROM `' . _DB_PREFIX_ . 'category` c2
				' . Shop::addSqlAssociation('category', 'c2') . '
				WHERE `' . _DB_PREFIX_ . 'category_product`.id_category = c2.id_category
					AND c2.nleft > ' . (int) $this->nleft . '
					AND c2.nright < ' . (int) $this->nright . '
					AND c2.active = 1
			)
		');
        if (!$nbProductRecursive) {
            return -1;
        }

        return $nbProductRecursive;
    }

    /**
     * @see self::getCategoryInformation()
     * @deprecated 1.7.0
     */
    public static function getCategoryInformations($idsCategory, $idLang = null)
    {
        return self::getCategoryInformation($idsCategory, $idLang);
    }

    /**
     * Get Category information.
     *
     * @param array $idsCategory Category IDs
     * @param int $idLang Language ID
     *
     * @return array|false Array with Category information
     *                     `false` if no Category found
     *
     * @since 1.7.0
     */
    public static function getCategoryInformation($idsCategory, $idLang = null)
    {
        if ($idLang === null) {
            $idLang = Context::getContext()->language->id;
        }

        if (!is_array($idsCategory) || !count($idsCategory)) {
            return false;
        }

        $categories = [];
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`, cl.`id_lang`
		FROM `' . _DB_PREFIX_ . 'category` c
		LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . ')
		' . Shop::addSqlAssociation('category', 'c') . '
		WHERE cl.`id_lang` = ' . (int) $idLang . '
		AND c.`id_category` IN (' . implode(',', array_map('intval', $idsCategory)) . ')');

        foreach ($results as $category) {
            $categories[$category['id_category']] = $category;
        }

        return $categories;
    }

    /**
     * Is parent Category available.
     *
     * @return bool Indicates whether the parent Category is available
     */
    public function isParentCategoryAvailable()
    {
        $id = Context::getContext()->shop->id;
        $idShop = $id ? $id : Configuration::get('PS_SHOP_DEFAULT');

        return (bool) Db::getInstance()->getValue('
		SELECT c.`id_category`
		FROM `' . _DB_PREFIX_ . 'category` c
		' . Shop::addSqlAssociation('category', 'c') . '
		WHERE category_shop.`id_shop` = ' . (int) $idShop . '
		AND c.`id_parent` = ' . (int) $this->id_parent);
    }

    /**
     * Add association between shop and categories.
     *
     * @param int $idShop Shop ID
     *
     * @return bool Indicates whether the association was successfully made
     */
    public function addShop($idShop)
    {
        $data = [];
        if (!$idShop) {
            foreach (Shop::getShops(false) as $shop) {
                if (!$this->existsInShop($shop['id_shop'])) {
                    $data[] = [
                        'id_category' => (int) $this->id,
                        'id_shop' => (int) $shop['id_shop'],
                    ];
                }
            }
        } elseif (!$this->existsInShop($idShop)) {
            $data[] = [
                'id_category' => (int) $this->id,
                'id_shop' => (int) $idShop,
            ];
        }

        return Db::getInstance()->insert('category_shop', $data);
    }

    /**
     * Get root Categories.
     *
     * @param int|null $idLang Language ID
     * @param bool $active Whether the root Category must be active
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null Root Categories
     */
    public static function getRootCategories($idLang = null, $active = true)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT DISTINCT(c.`id_category`), cl.`name`
		FROM `' . _DB_PREFIX_ . 'category` c
		LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (cl.`id_category` = c.`id_category` AND cl.`id_lang`=' . (int) $idLang . ')
		WHERE `is_root_category` = 1
		' . ($active ? 'AND `active` = 1' : ''));
    }

    /**
     * Get Categories without parent.
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null Categories without parent
     */
    public static function getCategoriesWithoutParent()
    {
        $cacheId = 'Category::getCategoriesWithoutParent_' . (int) Context::getContext()->language->id;
        if (!Cache::isStored($cacheId)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT DISTINCT c.*
			FROM `' . _DB_PREFIX_ . 'category` c
			LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = ' . (int) Context::getContext()->language->id . ')
			WHERE `level_depth` = 1');
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Is Root Category for a Shop.
     *
     * @return bool Indicates whether the current Category is a Root category for a Shop
     */
    public function isRootCategoryForAShop()
    {
        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `id_shop`
		FROM `' . _DB_PREFIX_ . 'shop`
		WHERE `id_category` = ' . (int) $this->id);
    }

    /**
     * Get Top Category.
     *
     * @param int|null $idLang Language ID
     *
     * @return Category Top Category
     */
    public static function getTopCategory($idLang = null)
    {
        if (null === $idLang) {
            $idLang = (int) Context::getContext()->language->id;
        }
        $cacheId = 'Category::getTopCategory_' . (int) $idLang;
        if (!Cache::isStored($cacheId)) {
            $idCategory = (int) Db::getInstance()->getValue('
			SELECT `id_category`
			FROM `' . _DB_PREFIX_ . 'category`
			WHERE `id_parent` = 0');
            $category = new Category($idCategory, $idLang);
            Cache::store($cacheId, $category);

            return $category;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Add position to current Category.
     *
     * @param int $position Position
     * @param int|null $idShop Shop ID
     *
     * @return bool Indicates whether the position was successfully added
     */
    public function addPosition($position, $idShop = null)
    {
        $position = (int) $position;
        $return = true;

        if (null !== $idShop) {
            $shopIds = [(int) $idShop];
        } else {
            if (Shop::getContext() != Shop::CONTEXT_SHOP) {
                $shopIds = Shop::getContextListShopID();
            } else {
                $id = Context::getContext()->shop->id;
                $shopIds = [$id ? $id : Configuration::get('PS_SHOP_DEFAULT')];
            }
        }

        foreach ($shopIds as $idShop) {
            $return &= Db::getInstance()->execute(
                sprintf(
                    'INSERT INTO `' . _DB_PREFIX_ . 'category_shop` ' .
                    '(`id_category`, `id_shop`, `position`) VALUES ' .
                    '(%d, %d, %d) ' .
                    'ON DUPLICATE KEY UPDATE `position` = %d',
                    (int) $this->id,
                    (int) $idShop,
                    $position,
                    $position
                )
            );
        }

        $return &= Db::getInstance()->execute(
            sprintf(
                'UPDATE `' . _DB_PREFIX_ . 'category` c ' .
                'SET c.`position`= %d WHERE c.id_category = %d',
                $position,
                (int) $this->id
            )
        );

        return $return;
    }

    /**
     * Get Shops by Category ID.
     *
     * @param int $idCategory Category ID
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null Array with Shop IDs
     */
    public static function getShopsByCategory($idCategory)
    {
        return Db::getInstance()->executeS('
		SELECT `id_shop`
		FROM `' . _DB_PREFIX_ . 'category_shop`
		WHERE `id_category` = ' . (int) $idCategory);
    }

    /**
     * Update Categories for a shop.
     *
     * @param string $categories Categories list to associate a shop
     * @param string $idShop Categories list to associate a shop
     *
     * @return array|false Update/insertion result
     *                     `false` if not successfully inserted/updated
     */
    public static function updateFromShop($categories, $idShop)
    {
        $shop = new Shop($idShop);
        // if array is empty or if the default category is not selected, return false
        if (!is_array($categories) || !count($categories) || !in_array($shop->id_category, $categories)) {
            return false;
        }

        // delete categories for this shop
        Category::deleteCategoriesFromShop($idShop);

        // and add $categories to this shop
        return Category::addToShop($categories, $idShop);
    }

    /**
     * Delete category from shop $id_shop.
     *
     * @param int $idShop Shop ID
     *
     * @return bool Indicates whether the current Category was successfully removed from the Shop
     */
    public function deleteFromShop($idShop)
    {
        return Db::getInstance()->execute('
		DELETE FROM `' . _DB_PREFIX_ . 'category_shop`
		WHERE `id_shop` = ' . (int) $idShop . '
		AND id_category = ' . (int) $this->id);
    }

    /**
     * Deletes all Categories from the Shop ID.
     *
     * @return bool Indicates whether the Categories have been successfully removed
     */
    public static function deleteCategoriesFromShop($idShop)
    {
        return Db::getInstance()->delete('category_shop', 'id_shop = ' . (int) $idShop);
    }

    /**
     * Add some categories to a shop.
     *
     * @param array $categories
     *
     * @return bool Indicates whether the Categories were successfully added to the given Shop
     */
    public static function addToShop(array $categories, $idShop)
    {
        if (!is_array($categories)) {
            return false;
        }
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'category_shop` (`id_category`, `id_shop`) VALUES';
        $tabCategories = [];
        foreach ($categories as $idCategory) {
            $tabCategories[] = new Category($idCategory);
            $sql .= '("' . (int) $idCategory . '", "' . (int) $idShop . '"),';
        }
        // removing last comma to avoid SQL error
        $sql = substr($sql, 0, strlen($sql) - 1);

        $return = Db::getInstance()->execute($sql);
        // we have to update position for every new entries
        foreach ($tabCategories as $category) {
            /* @var Category $category */
            $category->addPosition(Category::getLastPosition($category->id_parent, $idShop), $idShop);
        }

        return $return;
    }

    /**
     * Does the current Category exists in the given Shop.
     *
     * @param int $idShop Shop ID
     *
     * @return bool Indicates whether the current Category exists in the given Shop
     */
    public function existsInShop($idShop)
    {
        return (bool) Db::getInstance()->getValue('
		SELECT `id_category`
		FROM `' . _DB_PREFIX_ . 'category_shop`
		WHERE `id_category` = ' . (int) $this->id . '
		AND `id_shop` = ' . (int) $idShop, false);
    }

    /**
     * Indicates whether a category is ROOT for the shop.
     * The root category is the one with no parent. It's a virtual category.
     *
     * @return bool
     */
    public function isRootCategory(): bool
    {
        return 0 === (int) $this->id_parent;
    }
}
