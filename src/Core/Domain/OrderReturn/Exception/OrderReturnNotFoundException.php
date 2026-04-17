<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception;

/**
 * Is thrown when order return is not found in order return subdomain
 */
class OrderReturnNotFoundException extends OrderReturnException
{
}
