<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\AddProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Handles @see AddProductCommand using legacy object model
 */
#[AsCommandHandler]
final class AddProductHandler implements AddProductHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        Tools $tools
    ) {
        $this->productRepository = $productRepository;
        $this->tools = $tools;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddProductCommand $command): ProductId
    {
        $localizedNames = $command->getLocalizedNames();
        $localizedLinkRewrites = [];

        foreach ($localizedNames as $langId => $name) {
            if (empty($name)) {
                continue;
            }

            $localizedLinkRewrites[$langId] = $this->tools->linkRewrite($name);
        }

        $product = $this->productRepository->create(
            $command->getLocalizedNames(),
            $localizedLinkRewrites,
            $command->getProductType()->getValue(),
            $command->getShopId()
        );

        return new ProductId((int) $product->id);
    }
}
