<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\CommandHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\BulkEnableCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler\BulkEnableCmsPageCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CannotEnableCmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryNotFoundException;
use PrestaShopException;

/**
 * Class BulkEnableCmsPageCategoryCommand is responsible for enabling cms category pages.
 */
#[AsCommandHandler]
final class BulkEnableCmsPageCategoryHandler implements BulkEnableCmsPageCategoryHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function handle(BulkEnableCmsPageCategoryCommand $command)
    {
        try {
            foreach ($command->getCmsPageCategoryIds() as $cmsPageCategoryId) {
                $entity = new CMSCategory($cmsPageCategoryId->getValue());

                if (0 >= $entity->id) {
                    throw new CmsPageCategoryNotFoundException(sprintf('Cms category object with id "%s" has not been found for enabling status.', $cmsPageCategoryId->getValue()));
                }

                $entity->active = true;

                if (false === $entity->update()) {
                    throw new CannotEnableCmsPageCategoryException(sprintf('Unable to enable cms category object with id "%s"', $cmsPageCategoryId->getValue()));
                }
            }
        } catch (PrestaShopException $e) {
            throw new CmsPageCategoryException('Unexpected error occurred when handling bulk enable cms category', 0, $e);
        }
    }
}
