<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\SetCategoryIsEnabledCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\SetCategoryIsEnabledHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotUpdateCategoryStatusException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;

/**
 * @internal
 */
#[AsCommandHandler]
final class SetCategoryIsEnabledHandler implements SetCategoryIsEnabledHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CategoryNotFoundException
     * @throws CannotUpdateCategoryStatusException
     */
    public function handle(SetCategoryIsEnabledCommand $command)
    {
        $categoryId = $command->getCategoryId()->getValue();
        $entity = new Category($categoryId);

        if (!$entity->id) {
            throw new CategoryNotFoundException($command->getCategoryId(), sprintf('Category with id "%s" was not found', $categoryId));
        }

        if (!$entity->toggleStatus()) {
            throw new CannotUpdateCategoryStatusException(sprintf('Cannot update status for category with id "%s"', $categoryId));
        }
    }
}
