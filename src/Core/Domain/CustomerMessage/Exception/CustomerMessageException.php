<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

class CustomerMessageException extends DomainException
{
    public const ORDER_CUSTOMER_NOT_FOUND = 1;
}
