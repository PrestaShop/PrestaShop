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

namespace PrestaShop\PrestaShop\Core\CMS;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CmsPageCategoriesBreadcrumbTree;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CmsPageRootCategorySettings;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoriesForBreadcrumb;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Class CmsPageViewDataProvider provides cms page view data for cms listing page.
 */
class CmsPageViewDataProvider
{
    /**
     * @var CommandBusInterface
     */
    private $queryBuss;

    /**
     * @param CommandBusInterface $queryBuss
     */
    public function __construct(CommandBusInterface $queryBuss)
    {
        $this->queryBuss = $queryBuss;
    }

    /**
     * Gets view data.
     *
     * @param int $cmsCategoryParentId
     *
     * @return array
     */
    public function getView($cmsCategoryParentId)
    {
        return [
            'rootCategoryId' => CmsPageRootCategorySettings::ROOT_CMS_PAGE_CATEGORY_ID,
            'breadcrumb_tree' => $this->getBreadcrumbTree($cmsCategoryParentId),
        ];
    }

    /**
     * Gets breadcrumb tree which contains cms page categories. If the exception is raised when it returns empty array.
     *
     * @param int $cmsCategoryParentId
     *
     * @return array
     */
    private function getBreadcrumbTree($cmsCategoryParentId)
    {
        try {
            $cmsPageCategoryId = new CmsPageCategoryId($cmsCategoryParentId);
            /** @var CmsPageCategoriesBreadcrumbTree $result */
            $result = $this->queryBuss->handle(new GetCmsPageCategoriesForBreadcrumb($cmsPageCategoryId));

            return $result->getTree();
        } catch (CmsPageCategoryException $exception) {
            return [];
        }
    }
}
