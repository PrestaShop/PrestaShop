<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Exception;

use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Will be thrown if a locale is not supported in Legacy format
 */
final class UnsupportedLocaleException extends NotFoundResourceException
{
    /**
     * @param string $filePath the expected file path of the translations
     * @param string $locale the translation locale
     *
     * @return self
     */
    public static function fileNotFound($filePath, $locale)
    {
        $exceptionMessage = sprintf(
            'The locale "%s" is not supported, because we can\'t find the related file in the module:
            have you created the file "%s"?',
            $locale,
            $filePath
        );

        return new self($exceptionMessage);
    }

    /**
     * @param string $locale the translation locale
     *
     * @return self
     */
    public static function invalidLocale($locale)
    {
        $exceptionMessage = sprintf(
            'The provided locale `%s` is invalid.',
            $locale
        );

        return new self($exceptionMessage);
    }
}
