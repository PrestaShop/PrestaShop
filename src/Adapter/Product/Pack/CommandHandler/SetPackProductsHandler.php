<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Pack\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Pack\Update\ProductPackUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Command\SetPackProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\CommandHandler\SetPackProductsHandlerInterface;

/**
 * Handles @see SetPackProductsCommand using legacy object model
 */
#[AsCommandHandler]
final class SetPackProductsHandler implements SetPackProductsHandlerInterface
{
    /**
     * @var ProductPackUpdater
     */
    private $productPackUpdater;

    /**
     * @param ProductPackUpdater $productPackUpdater
     */
    public function __construct(
        ProductPackUpdater $productPackUpdater
    ) {
        $this->productPackUpdater = $productPackUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SetPackProductsCommand $command): void
    {
        $this->productPackUpdater->setPackProducts($command->getPackId(), $command->getProducts());
    }
}
