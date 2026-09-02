<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Module\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

/**
 * Base exception for Module sub-domain
 */
class ModuleException extends DomainException
{
    /**
     * When module cannot be used because it is disabled
     */
    public const IS_DISABLED = 1;

    /**
     * When module cannot be used because it is deleted
     */
    public const IS_DELETED = 2;
}
