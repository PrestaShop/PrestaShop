<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\QueryHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
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
#[AsQueryHandler]
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
            $cmsPageCategory->meta_title,
            $cmsPageCategory->link_rewrite,
            $shopIds
        );
    }
}
