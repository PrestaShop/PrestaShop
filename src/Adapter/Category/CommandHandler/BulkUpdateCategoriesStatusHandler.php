<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\BulkUpdateCategoriesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\BulkUpdateCategoriesStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotUpdateCategoryStatusException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;

/**
 * Class ChangeCategoriesStatusHandler.
 *
 * @internal
 */
#[AsCommandHandler]
final class BulkUpdateCategoriesStatusHandler implements BulkUpdateCategoriesStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotUpdateCategoryStatusException
     * @throws CategoryNotFoundException
     */
    public function handle(BulkUpdateCategoriesStatusCommand $command)
    {
        foreach ($command->getCategoryIds() as $categoryId) {
            $entity = new Category($categoryId->getValue());
            $entity->active = $command->getNewStatus();

            if (!$entity->id) {
                throw new CategoryNotFoundException($categoryId, sprintf('Category with id "%s" was not found', $categoryId->getValue()));
            }

            if (!$entity->update()) {
                throw new CannotUpdateCategoryStatusException(sprintf('Cannot update status for category with id "%s"', $categoryId->getValue()));
            }
        }
    }
}
