<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Cart\Calculator;

use PrestaShop\PrestaShop\Core\Pricing\Cart\CartPriceInterface;

/**
 * A single step in the cart pricing pipeline. Each implementation mutates the
 * CartPrice DTO in place and returns early when not relevant.
 *
 * @experimental
 */
interface CartCalculatorInterface
{
    public function compute(CartPriceInterface $cartPrice): void;
}
