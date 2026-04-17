<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Category\QueryHandler;

use Category;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryIsEnabled;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryHandler\GetCategoryIsEnabledHandlerInterface;

/**
 * @internal
 */
#[AsQueryHandler]
final class GetCategoryIsEnabledHandler implements GetCategoryIsEnabledHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetCategoryIsEnabled $query)
    {
        $categoryId = $query->getCategoryId()->getValue();
        $category = new Category($categoryId);

        if ($category->id !== $categoryId) {
            throw new CategoryNotFoundException($query->getCategoryId(), sprintf('Category with id "%s" was not found.', $categoryId));
        }

        return (bool) $category->active;
    }
}
