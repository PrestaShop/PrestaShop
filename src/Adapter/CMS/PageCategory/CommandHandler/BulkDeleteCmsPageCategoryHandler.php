<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\CommandHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\BulkDeleteCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler\BulkDeleteCmsPageCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CannotDeleteCmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryNotFoundException;
use PrestaShopException;

/**
 * Class BulkDeleteCmsPageCategoryHandler is responsible for deleting multiple cms page categories.
 */
#[AsCommandHandler]
final class BulkDeleteCmsPageCategoryHandler implements BulkDeleteCmsPageCategoryHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function handle(BulkDeleteCmsPageCategoryCommand $command)
    {
        try {
            foreach ($command->getCmsPageCategoryIds() as $cmsPageCategoryId) {
                $entity = new CMSCategory($cmsPageCategoryId->getValue());

                if (0 >= $entity->id) {
                    throw new CmsPageCategoryNotFoundException(sprintf('Cms category object with id "%s" has not been found for deleting.', $cmsPageCategoryId->getValue()));
                }

                if (false === $entity->delete()) {
                    throw new CannotDeleteCmsPageCategoryException(sprintf('Unable to delete  cms category object with id "%s"', $cmsPageCategoryId->getValue()), CannotDeleteCmsPageCategoryException::FAILED_BULK_DELETE);
                }
            }
        } catch (PrestaShopException $e) {
            throw new CmsPageCategoryException('Unexpected error occurred when handling bulk delete cms category', 0, $e);
        }
    }
}
