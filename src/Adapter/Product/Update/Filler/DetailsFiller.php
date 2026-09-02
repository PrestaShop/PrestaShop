<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Update\Filler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use Product;

/**
 * Fills product properties which can be considered as product details
 */
class DetailsFiller implements ProductFillerInterface
{
    /**
     * {@inheritDoc}
     */
    public function fillUpdatableProperties(Product $product, UpdateProductCommand $command): array
    {
        $updatableProperties = [];

        if (null !== $command->getGtin()) {
            $product->ean13 = $command->getGtin()->getValue();
            $updatableProperties[] = 'ean13';
        }

        if (null !== $command->getIsbn()) {
            $product->isbn = $command->getIsbn()->getValue();
            $updatableProperties[] = 'isbn';
        }

        if (null !== $command->getMpn()) {
            $product->mpn = $command->getMpn();
            $updatableProperties[] = 'mpn';
        }

        if (null !== $command->getReference()) {
            $product->reference = $command->getReference()->getValue();
            $updatableProperties[] = 'reference';
        }

        if (null !== $command->getUpc()) {
            $product->upc = $command->getUpc()->getValue();
            $updatableProperties[] = 'upc';
        }

        return $updatableProperties;
    }
}
