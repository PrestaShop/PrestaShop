<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Address;

use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;

/**
 * Format addresses depending on the country of the address
 */
interface AddressFormatterInterface
{
    /**
     * @param AddressId $addressId
     *
     * @return string
     */
    public function format(AddressId $addressId): string;
}
