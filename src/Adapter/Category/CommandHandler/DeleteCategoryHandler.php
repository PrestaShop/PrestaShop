<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\DeleteCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotDeleteRootCategoryForShopException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\FailedToDeleteCategoryException;

/**
 * Class DeleteCategoryHandler.
 */
#[AsCommandHandler]
final class DeleteCategoryHandler extends AbstractDeleteCategoryHandler implements DeleteCategoryHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CategoryNotFoundException
     * @throws CannotDeleteRootCategoryForShopException
     * @throws FailedToDeleteCategoryException
     */
    public function handle(DeleteCategoryCommand $command)
    {
        $categoryIdValue = $command->getCategoryId()->getValue();
        $category = new Category($categoryIdValue);

        if (!$category->id) {
            throw new CategoryNotFoundException($command->getCategoryId(), sprintf('Category with id %s cannot be found.', var_export($categoryIdValue, true)));
        }

        if ($category->isRootCategoryForAShop()) {
            throw new CannotDeleteRootCategoryForShopException(sprintf('Shop\'s root category with id %s cannot be deleted.', var_export($categoryIdValue, true)));
        }

        if (!$category->delete()) {
            throw new FailedToDeleteCategoryException(sprintf('Failed to delete category with id %s', var_export($categoryIdValue, true)));
        }

        $this->updateProductCategories([
            (int) $category->id_parent => [$categoryIdValue],
        ], $command->getDeleteMode());
    }
}
