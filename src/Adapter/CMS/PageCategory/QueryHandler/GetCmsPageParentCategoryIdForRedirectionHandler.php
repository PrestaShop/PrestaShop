<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\QueryHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageParentCategoryIdForRedirection;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryHandler\GetCmsPageParentCategoryIdForRedirectionHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;
use PrestaShopException;

/**
 * Class GetCmsPageParentCategoryIdForRedirectionHandler is responsible for providing cms page categories parent id
 * for redirecting to the right controller after create, edit, delete, toggle actions.
 */
#[AsQueryHandler]
final class GetCmsPageParentCategoryIdForRedirectionHandler implements GetCmsPageParentCategoryIdForRedirectionHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function handle(GetCmsPageParentCategoryIdForRedirection $query)
    {
        try {
            $entity = new CMSCategory($query->getCmsPageCategoryId()->getValue());
            $parentId = (int) $entity->id_parent;
        } catch (PrestaShopException) {
            $parentId = CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID;
        }

        return new CmsPageCategoryId($parentId);
    }
}
