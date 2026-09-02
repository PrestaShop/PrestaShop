<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\BulkDeleteCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\BulkDeleteCategoriesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotDeleteRootCategoryForShopException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\FailedToDeleteCategoryException;

/**
 * Class BulkDeleteCategoriesHandler.
 */
#[AsCommandHandler]
final class BulkDeleteCategoriesHandler extends AbstractDeleteCategoryHandler implements BulkDeleteCategoriesHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CategoryNotFoundException
     * @throws CannotDeleteRootCategoryForShopException
     * @throws FailedToDeleteCategoryException
     */
    public function handle(BulkDeleteCategoriesCommand $command)
    {
        $deletedCategoryIdsByParent = [];
        foreach ($command->getCategoryIds() as $categoryId) {
            $category = new Category($categoryId->getValue());

            if (!$category->id) {
                throw new CategoryNotFoundException($categoryId, sprintf('Category with id %s cannot be found.', var_export($categoryId->getValue(), true)));
            }

            if ($category->isRootCategoryForAShop()) {
                throw new CannotDeleteRootCategoryForShopException(sprintf('Shop\'s root category with id %s cannot be deleted.', var_export($categoryId->getValue(), true)));
            }

            if (!$category->delete()) {
                throw new FailedToDeleteCategoryException(sprintf('Failed to delete category with id %s', var_export($categoryId->getValue(), true)));
            }

            $deletedCategoryIdsByParent[(int) $category->id_parent][] = $categoryId->getValue();
        }

        if (empty($deletedCategoryIdsByParent)) {
            return;
        }

        $this->updateProductCategories($deletedCategoryIdsByParent, $command->getDeleteMode());
    }
}
