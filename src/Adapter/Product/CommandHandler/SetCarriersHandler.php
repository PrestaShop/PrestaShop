<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetCarriersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\SetCarriersHandlerInterface;

/**
 * Handles @var SetCarriersCommand using repository
 */
#[AsCommandHandler]
class SetCarriersHandler implements SetCarriersHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(SetCarriersCommand $command): void
    {
        $this->productRepository->setCarrierReferences(
            $command->getProductId(),
            $command->getCarrierReferenceIds(),
            $command->getShopConstraint()
        );
    }
}
