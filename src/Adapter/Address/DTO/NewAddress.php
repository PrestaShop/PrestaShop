<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Address\DTO;

use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;

class NewAddress
{
    private int $customerId = 0;
    private int $manufacturerId = 0;
    private int $supplierId = 0;
    private ?int $stateId = null;
    private ?string $company = null;
    private ?string $vatNumber = null;
    private ?string $address2 = null;
    private ?string $other = null;
    private ?string $phone = null;
    private ?string $phoneMobile = null;
    private ?string $dni = null;
    private bool $deleted = false;

    public function __construct(
        private readonly CountryId $countryId,
        private readonly string $alias,
        private readonly string $lastname,
        private readonly string $firstname,
        private readonly string $address1,
        private readonly string $city,
        private readonly string $postcode,
    ) {
    }

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function setManufacturerId(int $manufacturerId): self
    {
        $this->manufacturerId = $manufacturerId;

        return $this;
    }

    public function setSupplierId(int $supplierId): self
    {
        $this->supplierId = $supplierId;

        return $this;
    }

    public function setStateId(?int $stateId): self
    {
        $this->stateId = $stateId;

        return $this;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function setVatNumber(?string $vatNumber): self
    {
        $this->vatNumber = $vatNumber;

        return $this;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function setOther(?string $other): self
    {
        $this->other = $other;

        return $this;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function setPhoneMobile(?string $phoneMobile): self
    {
        $this->phoneMobile = $phoneMobile;

        return $this;
    }

    public function setDni(?string $dni): self
    {
        $this->dni = $dni;

        return $this;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getCountryId(): CountryId
    {
        return $this->countryId;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getAddress1(): string
    {
        return $this->address1;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getStateId(): ?int
    {
        return $this->stateId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getManufacturerId(): int
    {
        return $this->manufacturerId;
    }

    public function getSupplierId(): int
    {
        return $this->supplierId;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function getOther(): ?string
    {
        return $this->other;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getPhoneMobile(): ?string
    {
        return $this->phoneMobile;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }
}
