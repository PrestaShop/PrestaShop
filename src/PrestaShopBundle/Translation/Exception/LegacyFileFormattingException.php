<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Exception;

use Symfony\Component\Translation\Exception\InvalidResourceException;

/**
 * Will be thrown if a legacy file for a locale is found, but invalid.
 */
final class LegacyFileFormattingException extends InvalidResourceException
{
    /**
     * @param string $filePath the expected file path of the translations
     * @param string $locale the translation locale
     *
     * @return self
     */
    public static function fileIsInvalid($filePath, $locale)
    {
        $exceptionMessage = sprintf(
            'The locale "%s" is not supported, because we have found an invalid file in the module.
            Have you updated the file "%s" manually?',
            $locale,
            $filePath
        );

        return new self($exceptionMessage);
    }
}
