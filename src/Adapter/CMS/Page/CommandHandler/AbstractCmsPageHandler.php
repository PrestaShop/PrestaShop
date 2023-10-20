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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\Page\CommandHandler;

use CMS;
use CMSCategory;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryNotFoundException;
use PrestaShopException;

/**
 * Abstraction which holds all common functions required for cms page functionality.
 *
 * @internal
 */
abstract class AbstractCmsPageHandler extends AbstractObjectModelHandler
{
    /**
     * Gets cms object if it exists. If it does not exist it throws exceptions.
     *
     * @param int $cmsId
     *
     * @return CMS
     *
     * @throws CmsPageException
     */
    protected function getCmsPageIfExistsById($cmsId)
    {
        try {
            $cms = new CMS($cmsId);

            if (0 >= $cms->id) {
                throw new CmsPageNotFoundException(sprintf('Cms page with id "%s" not found', $cmsId));
            }
        } catch (PrestaShopException $exception) {
            throw new CmsPageException(sprintf('An error occurred when trying to get cms page with id %s', $cmsId));
        }

        return $cms;
    }

    /**
     * Checks whether cms page category exists by provided id.
     *
     * @param int $cmsCategoryId
     *
     * @throws CmsPageCategoryException
     */
    protected function assertCmsCategoryExists($cmsCategoryId)
    {
        try {
            $cmsCategory = new CMSCategory($cmsCategoryId);
            if (0 >= $cmsCategory->id) {
                throw new CmsPageCategoryNotFoundException(sprintf('Cms page category with id "%s" not found', $cmsCategoryId));
            }
        } catch (PrestaShopException $exception) {
            throw new CmsPageCategoryException(sprintf('An error occurred when trying to get cms page category with id %s', $cmsCategoryId));
        }
    }
}
