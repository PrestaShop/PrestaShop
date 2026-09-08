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
            // Re-index unconditionally. updatePosition() shifts a range of siblings and can leave a
            // duplicate or a gap when the requested way and position do not match the stored order,
            // which the back office never produces but any other caller can. cleanPositions() is the
            // only normalizer that also rewrites category_shop.position, so it has to run every time.
            Category::cleanPositions((int) $category->id_parent);
        }
    }
}
