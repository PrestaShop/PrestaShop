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

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\QueryHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CmsPageCategoriesBreadcrumbTree;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CmsPageRootCategorySettings;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoriesForBreadcrumb;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryHandler\GetCmsPageCategoriesForBreadcrumbHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategory;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;
use PrestaShopException;

/**
 * Class GetCmsPageCategoriesForBreadcrumbHandler is responsible for providing required data for displaying cms page category
 * breadcrumbs.
 */
final class GetCmsPageCategoriesForBreadcrumbHandler implements GetCmsPageCategoriesForBreadcrumbHandlerInterface
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
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function handle(GetCmsPageCategoriesForBreadcrumb $query)
    {
        try {
            $currentCategory = new CMSCategory(
                $query->getCurrentCategoryId()->getValue(),
                $this->contextLanguageId
            );

            $rootCategory = new CMSCategory(
                CmsPageRootCategorySettings::ROOT_CMS_PAGE_CATEGORY_ID,
                $this->contextLanguageId
            );
        } catch (PrestaShopException $exception) {
            throw new CmsPageCategoryException(
                sprintf(
                    'An error occurred when finding cms category object with id "%s" or root category by id "%s"',
                    $query->getCurrentCategoryId()->getValue(),
                    CmsPageRootCategorySettings::ROOT_CMS_PAGE_CATEGORY_ID
                ),
                0,
                $exception
            );
        }

        $rootCategoryData = [
            'id_cms_category' => CmsPageRootCategorySettings::ROOT_CMS_PAGE_CATEGORY_ID,
            'name' => $rootCategory->name,
        ];

        if (CmsPageRootCategorySettings::ROOT_CMS_PAGE_CATEGORY_ID === $query->getCurrentCategoryId()->getValue()) {
            return new CmsPageCategoriesBreadcrumbTree([
                new CmsPageCategory(
                    new CmsPageCategoryId($rootCategoryData['id_cms_category']),
                    $rootCategoryData['name']
                ),
            ]);
        }

        $parentCategories = $currentCategory->getParentsCategories($this->contextLanguageId);
        $parentCategories[] = $rootCategoryData;
        $parentCategories = array_reverse($parentCategories);

        $categories = [];
        foreach ($parentCategories as $category) {
            $categories[] = new CmsPageCategory(
                new CmsPageCategoryId($category['id_cms_category']),
                $category['name']
            );
        }

        return new CmsPageCategoriesBreadcrumbTree($categories);
    }
}
