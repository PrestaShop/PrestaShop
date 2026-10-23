<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception;

/**
 * Thrown when a product line (order detail and customization) is not part of the order return
 */
class OrderReturnProductNotFoundException extends OrderReturnNotFoundException
{
}
