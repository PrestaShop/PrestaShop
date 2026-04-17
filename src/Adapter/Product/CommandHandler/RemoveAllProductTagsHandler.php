<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductTagUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\RemoveAllProductTagsHandlerInterface;

#[AsCommandHandler]
final class RemoveAllProductTagsHandler implements RemoveAllProductTagsHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductTagUpdater
     */
    private $productTagUpdater;

    /**
     * @param ProductRepository $productRepository
     * @param ProductTagUpdater $productTagUpdater
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductTagUpdater $productTagUpdater
    ) {
        $this->productRepository = $productRepository;
        $this->productTagUpdater = $productTagUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RemoveAllProductTagsCommand $command): void
    {
        $this->productTagUpdater->setProductTags(
            $this->productRepository->getProductByDefaultShop($command->getProductId()),
            []
        );
    }
}
