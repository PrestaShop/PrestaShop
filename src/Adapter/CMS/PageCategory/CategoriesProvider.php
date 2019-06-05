<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory;

use Db;
use DbQuery;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;
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
        $this->contextShopIds = array_map(function ($item) { return (int) $item; }, $contextShopIds);
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
        return $this->collectNestedCategoriesIdsAndNames(CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID);
    }

    /**
     * Gets recursive category ids and names
     *
     * @param int $cmsPageCategoryId
     *
     * @return array - [
     *               'id_cms_category' => 1,
     *               'name' => 'root category',
     *               'children' => [...]
     *               ]
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
        $childCategories = is_array($childCategories) ? $childCategories : [];

        foreach ($childCategories as $childCategory) {
            $categories['children'][] = $this->collectNestedCategoriesIdsAndNames($childCategory['id_cms_category']);
        }

        return $categories;
    }
}
