<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\UpdateCategoryPositionCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\UpdateCategoryPositionHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;

/**
 * Updates category position using legacy object model
 */
#[AsCommandHandler]
final class UpdateCategoryPositionHandler implements UpdateCategoryPositionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCategoryPositionCommand $command)
    {
        $parentCategoryId = $command->getParentCategoryId()->getValue();
        $categoryId = $command->getCategoryId()->getValue();

        $position = null;

        foreach ($command->getPositions() as $key => $value) {
            [, $positionParentCategoryId, $positionCategoryId] = explode('_', $value);

            if ((int) $positionParentCategoryId === $parentCategoryId && (int) $positionCategoryId === $categoryId) {
                $position = $key;

                break;
            }
        }

        if (null === $position) {
            throw new CategoryException('Category position cannot be updated');
        }

        $category = new Category($categoryId);

        if (!$category->id) {
            throw new CategoryNotFoundException($command->getCategoryId(), sprintf('Category with id "%s" was not found', $categoryId));
        }

        if ($category->updatePosition((bool) $command->getWay(), $position)) {
            /* Position '0' was not found in given positions so try to reorder parent category */
            if (!$command->isFoundFirst()) {
                Category::cleanPositions((int) $category->id_parent);
            }
        }
    }
}
