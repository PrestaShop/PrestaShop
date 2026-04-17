<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Address;

use Address;
use AddressFormat;
use PrestaShop\PrestaShop\Core\Address\AddressFormatterInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;

class AddressFormatter implements AddressFormatterInterface
{
    /**
     * @param AddressId $addressId
     *
     * @return string
     */
    public function format(AddressId $addressId): string
    {
        return AddressFormat::generateAddress(
            new Address($addressId->getValue())
        );
    }
}
