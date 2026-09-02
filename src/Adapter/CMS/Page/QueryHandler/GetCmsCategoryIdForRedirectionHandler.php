<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\Page\QueryHandler;

use PrestaShop\PrestaShop\Adapter\CMS\Page\CommandHandler\AbstractCmsPageHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Query\GetCmsCategoryIdForRedirection;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\QueryHandler\GetCmsCategoryIdHandlerForRedirectionInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * This class is used for getting the id which is used later on to redirect to the right page after certain controller
 * actions.
 */
#[AsQueryHandler]
final class GetCmsCategoryIdForRedirectionHandler extends AbstractCmsPageHandler implements GetCmsCategoryIdHandlerForRedirectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetCmsCategoryIdForRedirection $query)
    {
        try {
            $cms = $this->getCmsPageIfExistsById($query->getCmsPageId()->getValue());
            $categoryId = (int) $cms->id_cms_category;
        } catch (CmsPageException) {
            $categoryId = CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID;
        }

        return new CmsPageCategoryId($categoryId);
    }
}
