<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        } catch (PrestaShopException) {
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
        } catch (PrestaShopException) {
            throw new CmsPageCategoryException(sprintf('An error occurred when trying to get cms page category with id %s', $cmsCategoryId));
        }
    }
}
