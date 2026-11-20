<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Language\Pack\Import\Exception;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Thrown when an archive offered as a translation pack is not one.
 */
class InvalidTranslationPackException extends CoreException
{
    public const NOT_READABLE = 1;
    public const NOT_AN_ARCHIVE = 2;
    public const EMPTY_ARCHIVE = 3;
    public const UNEXPECTED_ENTRY = 4;
    public const MIXED_LOCALES = 5;
    public const MALFORMED_LOCALE = 6;
    public const TOO_LARGE = 7;
}
