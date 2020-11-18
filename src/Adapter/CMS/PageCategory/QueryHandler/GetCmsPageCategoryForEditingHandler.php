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

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\QueryHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult\EditableCmsPageCategory;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryHandler\GetCmsPageCategoryForEditingHandlerInterface;
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
                throw new CmsPageCategoryNotFoundException(
                    sprintf(
                        'Cms category object with id "%s" has not been found',
                        $query->getCmsPageCategoryId()->getValue()
                    )
                );
            }

            $shopIds = is_array($cmsPageCategory->getAssociatedShops()) ? $cmsPageCategory->getAssociatedShops() : [];
        } catch (PrestaShopException $exception) {
            throw new CmsPageCategoryException(
                sprintf(
                    'An error occurred when retrieving cms page category data with id %s',
                    $query->getCmsPageCategoryId()->getValue()
                ),
                0,
                $exception
            );
        }

        return new EditableCmsPageCategory(
            is_array($cmsPageCategory->name) ? $cmsPageCategory->name : [],
            $cmsPageCategory->active,
            (int) $cmsPageCategory->id_parent,
            is_array($cmsPageCategory->description) ? $cmsPageCategory->description : [],
            is_array($cmsPageCategory->meta_description) ? $cmsPageCategory->meta_description : [],
            is_array($cmsPageCategory->meta_keywords) ? $cmsPageCategory->meta_keywords : [],
            is_array($cmsPageCategory->meta_title) ? $cmsPageCategory->meta_title : [],
            is_array($cmsPageCategory->link_rewrite) ? $cmsPageCategory->link_rewrite : [],
            $shopIds
        );
    }
}
