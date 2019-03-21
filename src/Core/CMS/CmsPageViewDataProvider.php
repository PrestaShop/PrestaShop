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
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult\BreadcrumbTree;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CmsPageRootCategorySettings;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoriesForBreadcrumb;

/**
 * Class CmsPageViewDataProvider provides cms page view data for cms listing page.
 */
final class CmsPageViewDataProvider implements CmsPageViewDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(CommandBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function getView($cmsCategoryParentId)
    {
        return [
            'root_category_id' => CmsPageRootCategorySettings::ROOT_CMS_PAGE_CATEGORY_ID,
            'breadcrumb_tree' => $this->getBreadcrumbTree($cmsCategoryParentId),
        ];
    }

    /**
     * Gets breadcrumb tree which contains cms page categories. If the exception is raised when it returns empty array.
     *
     * @param int $cmsCategoryParentId
     *
     * @return BreadcrumbTree|array
     *
     * @throws CmsPageCategoryException
     */
    private function getBreadcrumbTree($cmsCategoryParentId)
    {
        return $this->queryBus->handle(new GetCmsPageCategoriesForBreadcrumb($cmsCategoryParentId));
    }
}
