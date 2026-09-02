<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\Page\QueryHandler;

use Link;
use PrestaShop\PrestaShop\Adapter\CMS\Page\CommandHandler\AbstractCmsPageHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
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
#[AsQueryHandler]
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
                $cms->link_rewrite,
                $cms->content,
                $cms->indexation,
                $cms->active,
                $cms->getAssociatedShops(),
                $this->link->getCMSLink($cms, null, null, $this->langId)
            );
        } catch (PrestaShopException) {
            throw new CmsPageException(sprintf('An error occurred when getting cms page for editing with id "%s"', $cmsPageId));
        }
    }
}
