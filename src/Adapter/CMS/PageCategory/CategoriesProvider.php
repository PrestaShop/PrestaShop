<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 19.2.20
 * Time: 18.02
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory;

use CMSCategory;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CmsPageRootCategorySettings;

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
     * @param int $contextLanguageId
     */
    public function __construct($contextLanguageId)
    {
        $this->contextLanguageId = $contextLanguageId;
    }

    /**
     * Gets all nested cms page categories.
     *
     * @return array
     */
    public function getAllNestedCategories()
    {
        //todo: write own sql here
        $result = CMSCategory::getRecurseCategory(
            $this->contextLanguageId,
            CmsPageRootCategorySettings::ROOT_CMS_PAGE_CATEGORY_ID,
            0
        );

        return is_array($result) ? $result : [];
    }
}
