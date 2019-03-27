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

namespace PrestaShop\PrestaShop\Adapter\CMS\Page\QueryHandler;

use PrestaShop\PrestaShop\Adapter\CMS\Page\CommandHandler\AbstractCmsPageHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Query\getCmsPageForEditing;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\QueryHandler\GetCmsPageForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\QueryResult\EditableCmsPage;

/**
 * Gets cms page for editing
 */
final class GetCmsPageForEditingHandler extends AbstractCmsPageHandler implements GetCmsPageForEditingHandlerInterface
{

    /**
     * @param GetCmsPageForEditing $query
     *
     * @return EditableCmsPage
     */
    public function handle(getCmsPageForEditing $query)
    {
        $cms = $this->getCmsPageIfExistsById($query->getCmsPageId()->getValue());

        return new EditableCmsPage(
            (int) $cms->id_cms_category,
            $cms->meta_title,
            $cms->head_seo_title,
            $cms->meta_description,
            $cms->meta_keywords,
            $cms->link_rewrite,
            $cms->content,
            $cms->indexation,
            $cms->active,
            $cms->getAssociatedShops()
        );

    }
}
