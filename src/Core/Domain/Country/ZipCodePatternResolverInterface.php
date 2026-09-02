<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Country;

/**
 * Call responsible for resolving country zip code format and returning it as other usable patterns
 */
interface ZipCodePatternResolverInterface
{
    /**
     * @param string $format
     * @param string $isoCode
     *
     * @return string
     */
    public function getRegexPattern(string $format, string $isoCode): string;

    /**
     * @param string $format
     * @param string $isoCode
     *
     * @return string
     */
    public function getHumanReadablePattern(string $format, string $isoCode): string;
}
