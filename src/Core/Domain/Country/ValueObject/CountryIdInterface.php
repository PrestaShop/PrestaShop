<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Country\ValueObject;

/**
 * Provides country id value
 */
interface CountryIdInterface
{
    public function getValue(): int;
}
