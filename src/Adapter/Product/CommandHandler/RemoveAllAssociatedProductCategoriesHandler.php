<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Update\ProductCategoryUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\RemoveAllAssociatedProductCategoriesHandlerInterface;

/**
 * Handles @see RemoveAllAssociatedProductCategoriesCommand using legacy object model
 */
#[AsCommandHandler]
final class RemoveAllAssociatedProductCategoriesHandler implements RemoveAllAssociatedProductCategoriesHandlerInterface
{
    /**
     * @var ProductCategoryUpdater
     */
    private $productCategoryUpdater;

    /**
     * @param ProductCategoryUpdater $productCategoryUpdater
     */
    public function __construct(
        ProductCategoryUpdater $productCategoryUpdater
    ) {
        $this->productCategoryUpdater = $productCategoryUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RemoveAllAssociatedProductCategoriesCommand $command): void
    {
        $this->productCategoryUpdater->removeAllCategories($command->getProductId(), $command->getShopConstraint());
    }
}
