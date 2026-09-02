<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

/**
 * Base exception for order message subdomain
 */
class OrderMessageException extends DomainException
{
    public const FAILED_DELETE = 1;
    public const FAILED_BULK_DELETE = 2;
}
