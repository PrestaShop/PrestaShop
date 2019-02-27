<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 19.2.20
 * Time: 18.02
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory;

use CMSCategory;
use Db;
use DbQuery;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CmsPageRootCategorySettings;
use PrestaShopDatabaseException;

/**
 * Class CategoriesProvider is responsible for providing cms page categories data.
 */
class CategoriesProvider
{
    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @param int $contextLanguageId
     * @param array $contextShopIds
     */
    public function __construct(
        $contextLanguageId,
        array $contextShopIds
    ) {
        $this->contextLanguageId = (int) $contextLanguageId;
        $this->contextShopIds = array_map(function ($item){ return (int) $item; }, $contextShopIds);
    }

    /**
     * Gets all nested cms page categories.
     *
     * @return array
     *
     * @throws PrestaShopDatabaseException
     */
    public function getAllNestedCategories()
    {
        return $this->collectNestedCategoriesIdsAndNames(CmsPageRootCategorySettings::ROOT_CMS_PAGE_CATEGORY_ID);
    }

    /**
     * Gets recursive category ids and names
     *
     * @param int $cmsPageCategoryId
     *
     * @return array - [
     *  'id_cms_category' => 1,
     *  'name' => 'root category',
     *  'children' => [...]
     * ]
     *
     * @throws PrestaShopDatabaseException
     */
    private function collectNestedCategoriesIdsAndNames($cmsPageCategoryId)
    {
        $mainCategoryQuery = new DbQuery();
        $mainCategoryQuery
            ->select('c.`id_cms_category`, cl.`name`')
            ->from('cms_category', 'c')
            ->innerJoin(
                'cms_category_lang',
                'cl',
                'cl.`id_cms_category` = c.`id_cms_category`'
            )
            ->where('c.`id_cms_category` = ' . (int) $cmsPageCategoryId)
            ->where('cl.`id_lang` = ' . $this->contextLanguageId)
            ->where('cl.`id_shop` IN (' . implode(',', $this->contextShopIds) . ')')
            ->groupBy('c.`id_cms_category`')
        ;

        $result = Db::getInstance()->getRow($mainCategoryQuery);
        $categories = is_array($result) ? $result : [];

        $childrenQuery = new DbQuery();
        $childrenQuery
            ->select('c.`id_cms_category`, cl.`name`')
            ->from('cms_category', 'c')
            ->innerJoin(
                'cms_category_lang',
                'cl',
                'cl.`id_cms_category` = c.`id_cms_category`'
            )
            ->where('c.`id_parent` = ' . (int) $cmsPageCategoryId)
            ->where('cl.`id_lang` = ' . $this->contextLanguageId)
            ->where('cl.`id_shop` IN (' . implode(',', $this->contextShopIds) . ')')
            ->groupBy('c.`id_cms_category`')
        ;

        $childCategories = Db::getInstance()->executeS($childrenQuery);
        $childCategories = is_array($childCategories) ? $childCategories: [];

        foreach ($childCategories as $childCategory) {
            $categories['children'][] = $this->collectNestedCategoriesIdsAndNames($childCategory['id_cms_category']);
        }

        return $categories;
    }
}
