<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Country;

/**
 * Call responsible for resolving country zip code format and returning it as other usable patterns
 */
final class ZipCodePatternResolver implements ZipCodePatternResolverInterface
{
    /**
     * @param string $format
     * @param string $isoCode
     *
     * @return string
     */
    public function getRegexPattern(string $format, string $isoCode): string
    {
        return str_replace(['N', 'L', 'C'], ['[0-9]', '[a-zA-Z]', $isoCode], '/^' . $format . '$/ui');
    }

    /**
     * @param string $format
     * @param string $isoCode
     *
     * @return string
     */
    public function getHumanReadablePattern(string $format, string $isoCode): string
    {
        return str_replace(['N', 'L',  'C'], ['0', 'A', $isoCode], $format);
    }
}
