<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Base class to test a product command builder
 */
abstract class AbstractProductCommandBuilderTestCase extends AbstractMultiShopCommandsBuilderTestCase
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @return ProductId
     */
    protected function getProductId(): ProductId
    {
        if (null === $this->productId) {
            $this->productId = new ProductId(42);
        }

        return $this->productId;
    }
}
