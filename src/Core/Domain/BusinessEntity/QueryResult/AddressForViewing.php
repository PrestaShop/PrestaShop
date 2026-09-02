<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult;

use PrestaShopBundle\Entity\Enum\AddressTypeEnum;

class AddressForViewing
{
    public function __construct(
        private readonly int $addressId,
        private readonly string $alias,
        private readonly string $formattedAddress,
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

    public function getFormattedAddress(): string
    {
        return $this->formattedAddress;
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
