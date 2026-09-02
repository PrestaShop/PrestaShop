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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductTagsHandlerInterface;

/**
 * Handles UpdateProductTagsCommand using legacy object model
 */
#[AsCommandHandler]
final class SetProductTagsHandler implements UpdateProductTagsHandlerInterface
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
    public function handle(SetProductTagsCommand $command): void
    {
        $product = $this->productRepository->getProductByDefaultShop($command->getProductId());
        $this->productTagUpdater->setProductTags($product, $command->getLocalizedTagsList());
    }
}
