<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Tax\Ecotax;

use PrestaShop\PrestaShop\Core\Tax\Ecotax\ProductEcotaxResetterInterface;
use Product;

/**
 * Resets ecotax for products using legacy object model
 */
final class ProductEcotaxResetter implements ProductEcotaxResetterInterface
{
    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        Product::resetEcoTax();
    }
}
