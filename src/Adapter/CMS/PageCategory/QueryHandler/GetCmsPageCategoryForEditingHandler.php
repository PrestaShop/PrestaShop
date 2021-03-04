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

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\QueryHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryHandler\GetCmsPageCategoryForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult\EditableCmsPageCategory;
use PrestaShopException;

/**
 * Class GetCmsPageCategoryForEditingHandler is responsible for retrieving cms page category form data.
 *
 * @internal
 */
final class GetCmsPageCategoryForEditingHandler implements GetCmsPageCategoryForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function handle(GetCmsPageCategoryForEditing $query)
    {
        try {
            $cmsPageCategory = new CMSCategory($query->getCmsPageCategoryId()->getValue());

            if (0 >= $cmsPageCategory->id) {
                throw new CmsPageCategoryNotFoundException(sprintf('Cms category object with id "%s" has not been found', $query->getCmsPageCategoryId()->getValue()));
            }

            $shopIds = $cmsPageCategory->getAssociatedShops();
        } catch (PrestaShopException $exception) {
            throw new CmsPageCategoryException(sprintf('An error occurred when retrieving cms page category data with id %s', $query->getCmsPageCategoryId()->getValue()), 0, $exception);
        }

        return new EditableCmsPageCategory(
            $cmsPageCategory->name,
            $cmsPageCategory->active,
            (int) $cmsPageCategory->id_parent,
            $cmsPageCategory->description,
            $cmsPageCategory->meta_description,
            $cmsPageCategory->meta_keywords,
            $cmsPageCategory->meta_title,
            $cmsPageCategory->link_rewrite,
            $shopIds
        );
    }
}
