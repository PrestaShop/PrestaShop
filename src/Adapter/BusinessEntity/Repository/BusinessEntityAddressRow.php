<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\BusinessEntity\Repository;

use PrestaShopBundle\Entity\Enum\AddressTypeEnum;

/**
 * A business entity to address link, with the alias resolved from the address table.
 * Typed counterpart of what BusinessEntityAddressRepository::getAddresses() selects.
 */
class BusinessEntityAddressRow
{
    public function __construct(
        private readonly int $addressId,
        private readonly string $alias,
        private readonly AddressTypeEnum $addressType,
        private readonly bool $isDefault,
    ) {
    }

    public function getAddressId(): int
    {
        return $this->addressId;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getAddressType(): AddressTypeEnum
    {
        return $this->addressType;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
