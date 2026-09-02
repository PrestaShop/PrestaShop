<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Exception;

use RuntimeException;

class InvalidLanguageException extends RuntimeException
{
    public const LOCALE_NOT_FOUND = 1;

    public static function localeNotFound($locale)
    {
        return new static(sprintf('The locale "%s" is not available', $locale), self::LOCALE_NOT_FOUND);
    }
}
