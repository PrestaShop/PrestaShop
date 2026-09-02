<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

/**
 * Base exception for Currency sub-domain
 */
class CurrencyException extends DomainException
{
    /**
     * When currency cannot be used because it is disabled
     */
    public const IS_DISABLED = 1;

    /**
     * When currency cannot be used because it is deleted
     */
    public const IS_DELETED = 2;
}
