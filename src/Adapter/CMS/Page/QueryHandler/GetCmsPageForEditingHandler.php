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

namespace PrestaShop\PrestaShop\Adapter\CMS\Page\QueryHandler;

use Link;
use PrestaShop\PrestaShop\Adapter\CMS\Page\CommandHandler\AbstractCmsPageHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Query\GetCmsPageForEditing;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\QueryHandler\GetCmsPageForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\QueryResult\EditableCmsPage;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShopException;

/**
 * Gets cms page for editing
 */
final class GetCmsPageForEditingHandler extends AbstractCmsPageHandler implements GetCmsPageForEditingHandlerInterface
{
    /**
     * @var Link
     */
    private $link;

    /**
     * @var int
     */
    private $langId;

    /**
     * @param Link $link
     * @param int $langId
     */
    public function __construct(Link $link, $langId)
    {
        $this->link = $link;
        $this->langId = $langId;
    }

    /**
     * @param GetCmsPageForEditing $query
     *
     * @return EditableCmsPage
     *
     * @throws CmsPageException
     * @throws CmsPageCategoryException
     * @throws CmsPageNotFoundException
     */
    public function handle(GetCmsPageForEditing $query)
    {
        $cmsPageId = $query->getCmsPageId()->getValue();
        $cms = $this->getCmsPageIfExistsById($cmsPageId);

        try {
            return new EditableCmsPage(
                (int) $cms->id,
                (int) $cms->id_cms_category,
                $cms->meta_title,
                $cms->head_seo_title,
                $cms->meta_description,
                $cms->meta_keywords,
                $cms->link_rewrite,
                $cms->content,
                $cms->indexation,
                $cms->active,
                $cms->getAssociatedShops(),
                $this->link->getCMSLink($cms, null, null, $this->langId)
        );
        } catch (PrestaShopException $e) {
            throw new CmsPageException(sprintf('An error occurred when getting cms page for editing with id "%s"', $cmsPageId));
        }
    }
}
