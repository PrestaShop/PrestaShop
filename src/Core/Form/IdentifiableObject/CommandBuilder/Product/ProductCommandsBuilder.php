<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * This class builds a collection of product commands based on the form data and a list of ProductCommandBuilderInterface
 */
class ProductCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * @var iterable<ProductCommandsBuilderInterface>
     */
    private $commandBuilders;

    /**
     * @param iterable<ProductCommandsBuilderInterface> $commandBuilders
     */
    public function __construct(iterable $commandBuilders)
    {
        $this->commandBuilders = $commandBuilders;
    }

    /**
     * @param ProductId $productId
     * @param array $formData
     * @param ShopConstraint $singleShopConstraint
     *
     * @return array
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $commandCollection = [];
        foreach ($this->commandBuilders as $commandBuilder) {
            $commands = $commandBuilder->buildCommands($productId, $formData, $singleShopConstraint);

            if (empty($commands)) {
                continue;
            }

            $commandCollection = array_merge($commandCollection, $commands);
        }

        return $commandCollection;
    }
}
