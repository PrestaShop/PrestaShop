<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\CommandHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\DeleteCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler\DeleteCmsPageCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CannotDeleteCmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryNotFoundException;
use PrestaShopException;

/**
 * Class DeleteCmsPageCategoryHandler is responsible for deleting cms page category.
 */
#[AsCommandHandler]
final class DeleteCmsPageCategoryHandler implements DeleteCmsPageCategoryHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function handle(DeleteCmsPageCategoryCommand $command)
    {
        try {
            $entity = new CMSCategory($command->getCmsPageCategoryId()->getValue());

            if (0 >= $entity->id) {
                throw new CmsPageCategoryNotFoundException(sprintf('Cms category object with id "%s" has not been found for deletion.', $command->getCmsPageCategoryId()->getValue()));
            }

            if (false === $entity->delete()) {
                throw new CannotDeleteCmsPageCategoryException(sprintf('Unable to delete cms category object with id "%s"', $command->getCmsPageCategoryId()->getValue()), CannotDeleteCmsPageCategoryException::FAILED_DELETE);
            }
        } catch (PrestaShopException $exception) {
            throw new CmsPageCategoryException(sprintf('An error occurred when deleting cms category object with id "%s"', $command->getCmsPageCategoryId()->getValue()), 0, $exception);
        }
    }
}
