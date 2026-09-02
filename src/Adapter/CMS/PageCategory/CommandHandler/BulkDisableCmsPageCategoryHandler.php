<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\CommandHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\BulkDisableCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler\BulkDisableCmsPageCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CannotDisableCmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryNotFoundException;
use PrestaShopException;

/**
 * Class BulkDisableCmsPageCategoryHandler is responsible for deleting multiple cms page categories.
 */
#[AsCommandHandler]
final class BulkDisableCmsPageCategoryHandler implements BulkDisableCmsPageCategoryHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function handle(BulkDisableCmsPageCategoryCommand $command)
    {
        try {
            foreach ($command->getCmsPageCategoryIds() as $cmsPageCategoryId) {
                $entity = new CMSCategory($cmsPageCategoryId->getValue());

                if (0 >= $entity->id) {
                    throw new CmsPageCategoryNotFoundException(sprintf('Cms category object with id "%s" has not been found for disabling status.', $cmsPageCategoryId->getValue()));
                }

                $entity->active = false;

                if (false === $entity->update()) {
                    throw new CannotDisableCmsPageCategoryException(sprintf('Unable to disable cms category object with id "%s"', $cmsPageCategoryId->getValue()));
                }
            }
        } catch (PrestaShopException $e) {
            throw new CmsPageCategoryException('Unexpected error occurred when handling bulk disable cms category', 0, $e);
        }
    }
}
