<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Category;

use Category;
use Context;
use LogicException;
use ObjectModel;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Product;
use Shop;

/**
 * This class will provide data from DB / ORM about Category.
 */
class CategoryDataProvider
{
    /**
     * @var int
     */
    private $languageId;

    /**
     * @var array the list of existing active categories until root
     */
    private $categoryList;

    public function __construct(LegacyContext $context)
    {
        $this->languageId = $context->getLanguage()->id;
        $categories = Category::getSimpleCategoriesWithParentInfos($this->languageId);
        // index by categories and construct the categoryList
        foreach ($categories as $category) {
            $this->categoryList[$category['id_category']] = $category;
        }
    }

    /**
     * Get a category.
     *
     * @param int|null $idCategory
     * @param int|null $idLang
     * @param int|null $idShop
     *
     * @return Category
     *
     * @throws LogicException If the category id is not set
     */
    public function getCategory($idCategory = null, $idLang = null, $idShop = null)
    {
        if (!$idCategory) {
            throw new LogicException('You need to provide a category id', 5002);
        }

        $category = new Category($idCategory, $idLang, $idShop);
        $category->image = Context::getContext()->link->getCatImageLink($category->name, $category->id);

        return $category;
    }

    /**
     * Get all nested categories.
     *
     * @param int|null $root_category
     * @param bool|int $id_lang
     * @param bool $active
     * @param array|null $groups
     * @param bool $use_shop_restriction
     * @param string $sql_filter
     * @param string $sql_sort
     * @param string $sql_limit
     *
     * @return array categories
     */
    public function getNestedCategories($root_category = null, $id_lang = false, $active = true, $groups = null, $use_shop_restriction = true, $sql_filter = '', $sql_sort = '', $sql_limit = '')
    {
        if (!$id_lang) {
            $id_lang = $this->languageId;
        }

        return Category::getNestedCategories($root_category, $id_lang, $active, $groups, $use_shop_restriction, $sql_filter, $sql_sort, $sql_limit);
    }

    /**
     * Return available categories Names - excluding Root category.
     *
     * @param int|null $root_category
     * @param bool|int $id_lang
     * @param bool $active return only active categories
     * @param array|null $groups
     * @param bool $use_shop_restriction
     * @param string $sql_filter
     * @param string $sql_sort
     * @param string $sql_limit
     *
     * @return array Categories
     */
    public function getAllCategoriesName($root_category = null, $id_lang = false, $active = true, $groups = null, $use_shop_restriction = true, $sql_filter = '', $sql_sort = '', $sql_limit = '')
    {
        if (!$id_lang) {
            $id_lang = $this->languageId;
        }

        $categories = Category::getAllCategoriesName($root_category, $id_lang, $active, $groups, $use_shop_restriction, $sql_filter, $sql_sort, $sql_limit);
        array_shift($categories);

        return $categories;
    }

    /**
     * Return a simple array id/name of categories for a specified product.
     *
     * @param Product $product
     *
     * @return array Categories
     */
    public function getCategoriesByProduct(ObjectModel $product)
    {
        $productCategories = $product->getCategories();

        $results = [];
        foreach ($productCategories as $productCategory) {
            if (isset($this->categoryList[$productCategory])) {
                $category = $this->categoryList[$productCategory];
                $results[] = [
                    'id' => $category['id_category'],
                    'name' => $category['name'],
                    'breadcrumb' => $this->getBreadCrumb($category['id_category']),
                ];
                $productCategories[$category['name']] = $category['id_category'];
            }
        }

        return $results;
    }

    /**
     * Return a simple array id/name of categories.
     *
     * @return array Categories
     */
    public function getCategoriesWithBreadCrumb()
    {
        $results = [];
        foreach ($this->categoryList as $category) {
            $results[] = [
                'id' => $category['id_category'],
                'name' => $category['name'],
                'breadcrumb' => $this->getBreadCrumb($category['id_category']),
            ];
        }

        return $results;
    }

    /**
     * Construct the breadcrumb using the already constructed list of all categories.
     *
     * @param int $categoryId
     * @param string $delimiter
     *
     * @return string
     */
    public function getBreadCrumb($categoryId, $delimiter = ' > ')
    {
        $categories = $this->getParentNamesFromList($categoryId);
        $categories = array_reverse($categories, true);

        return implode($delimiter, $categories);
    }

    /**
     * @param int $categoryId
     *
     * @return array
     */
    public function getParentNamesFromList($categoryId)
    {
        $categories = [];

        while (isset($this->categoryList[$categoryId])) {
            $category = $this->categoryList[$categoryId];
            $categories[] = $category['name'];
            $categoryId = $category['id_parent'];
        }

        return $categories;
    }

    /**
     * Get Categories formatted like ajax_product_file.php using Category::getNestedCategories.
     *
     * @param string $query
     * @param int $limit
     * @param bool $nameAsBreadCrumb
     *
     * @return array
     */
    public function getAjaxCategories($query, $limit, $nameAsBreadCrumb = false)
    {
        if (empty($query)) {
            $query = '';
        } else {
            $query = "AND cl.name LIKE '%" . pSQL($query) . "%'";
        }

        $limitParam = '';
        if (is_int($limit)) {
            $limitParam = 'LIMIT ' . $limit;
        }

        $searchCategories = Category::getAllCategoriesName(
            null,
            Context::getContext()->language->id,
            true,
            null,
            true,
            $query,
            '',
            $limitParam
        );

        $results = [];
        foreach ($searchCategories as $category) {
            $breadCrumb = $this->getBreadCrumb($category['id_category']);

            if (file_exists(_PS_CAT_IMG_DIR_ . $category['id_category'] . '.jpg')) {
                $image = Context::getContext()->link->getCatImageLink($category['name'], $category['id_category']);
            } else {
                $image = Context::getContext()->link->getMediaLink(_THEME_CAT_DIR_ . Context::getContext()->language->iso_code . '-default-category_default.jpg');
            }

            $results[] = [
                'id' => $category['id_category'],
                'name' => ($nameAsBreadCrumb ? $breadCrumb : $category['name']),
                'breadcrumb' => $breadCrumb,
                'image' => $image,
            ];
        }

        return $results;
    }

    /**
     * @param int|null $idLang
     * @param Shop|null $shop
     *
     * @return Category
     */
    public function getRootCategory($idLang = null, ?Shop $shop = null)
    {
        return Category::getRootCategory($idLang, $shop);
    }
}
