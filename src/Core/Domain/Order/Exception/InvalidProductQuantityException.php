<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\Exception;

/**
 * Thrown when trying to add an invalid quantity or a product (<= 0)
 */
class InvalidProductQuantityException extends OrderException
{
}
