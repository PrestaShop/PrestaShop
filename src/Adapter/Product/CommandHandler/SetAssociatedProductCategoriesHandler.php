<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Update\ProductCategoryUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\SetAssociatedProductCategoriesHandlerInterface;

/**
 * Handles @var SetAssociatedProductCategoriesCommand using legacy object model
 */
#[AsCommandHandler]
final class SetAssociatedProductCategoriesHandler implements SetAssociatedProductCategoriesHandlerInterface
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
    public function handle(SetAssociatedProductCategoriesCommand $command): void
    {
        $this->productCategoryUpdater->updateCategories(
            $command->getProductId(),
            $command->getCategoryIds(),
            $command->getDefaultCategoryId(),
            $command->getShopConstraint()
        );
    }
}
