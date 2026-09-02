<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

/**
 * LanguageException is base exception for language subdomain
 */
class LanguageException extends DomainException
{
    /**
     * When language is not active
     */
    public const NOT_ACTIVE = 1;
}
