<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\NoStateId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateIdInterface;

abstract class AbstractBusinessEntityAddress
{
    private readonly CountryId $countryId;
    private readonly StateIdInterface $stateId;

    public function __construct(
        private readonly string $alias,
        private readonly string $address1,
        private readonly ?string $address2,
        private readonly string $city,
        private readonly string $postcode,
        int $countryId,
        private readonly bool $default,
        ?int $stateId,
        private readonly ?string $phone = null,
        private readonly ?string $phoneMobile = null,
    ) {
        $this->countryId = new CountryId($countryId);
        $this->stateId = $stateId !== null ? new StateId($stateId) : new NoStateId();
    }

    public function getCountryId(): CountryId
    {
        return $this->countryId;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getAddress1(): string
    {
        return $this->address1;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function getStateId(): StateIdInterface
    {
        return $this->stateId;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getPhoneMobile(): ?string
    {
        return $this->phoneMobile;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }
}
