<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\Command;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Edits supplier with provided data
 */
class EditSupplierCommand
{
    /**
     * @var SupplierId
     */
    private $supplierId;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string[]|null
     */
    private $localizedDescriptions;

    /**
     * @var string|null
     */
    private $address;

    /**
     * @var string|null
     */
    private $city;

    /**
     * @var string|null
     */
    private $address2;

    /**
     * @var int|null
     */
    private $countryId;

    /**
     * @var string|null
     */
    private $postCode;

    /**
     * @var int|null
     */
    private $stateId;

    /**
     * @var string|null
     */
    private $phone;

    /**
     * @var string|null
     */
    private $mobilePhone;

    /**
     * @var string[]|null
     */
    private $localizedMetaTitles;

    /**
     * @var string[]|null
     */
    private $localizedMetaDescriptions;

    /**
     * @var bool|null
     */
    private $enabled;

    /**
     * @var array|null
     */
    private $associatedShops;

    /**
     * @var string|null
     */
    private $dni;

    /**
     * @param int $supplierId
     *
     * @throws SupplierException
     */
    public function __construct(int $supplierId)
    {
        $this->supplierId = new SupplierId($supplierId);
    }

    /**
     * @return SupplierId
     */
    public function getSupplierId(): SupplierId
    {
        return $this->supplierId;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return EditSupplierCommand
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedDescriptions(): ?array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @param string[] $localizedDescriptions
     *
     * @return EditSupplierCommand
     */
    public function setLocalizedDescriptions(array $localizedDescriptions): self
    {
        $this->localizedDescriptions = $localizedDescriptions;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return EditSupplierCommand
     */
    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return EditSupplierCommand
     */
    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     *
     * @return EditSupplierCommand
     */
    public function setAddress2(string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     *
     * @return EditSupplierCommand
     */
    public function setCountryId(int $countryId): self
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    /**
     * @param string $postCode
     *
     * @return EditSupplierCommand
     */
    public function setPostCode(string $postCode): self
    {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStateId(): ?int
    {
        return $this->stateId;
    }

    /**
     * @param int $stateId
     *
     * @return EditSupplierCommand
     */
    public function setStateId(int $stateId): self
    {
        $this->stateId = $stateId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return EditSupplierCommand
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    /**
     * @param string $mobilePhone
     *
     * @return EditSupplierCommand
     */
    public function setMobilePhone(string $mobilePhone): self
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaTitles(): ?array
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @param string[] $localizedMetaTitles
     *
     * @return EditSupplierCommand
     */
    public function setLocalizedMetaTitles(array $localizedMetaTitles): self
    {
        $this->localizedMetaTitles = $localizedMetaTitles;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaDescriptions(): ?array
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @param string[] $localizedMetaDescriptions
     *
     * @return EditSupplierCommand
     */
    public function setLocalizedMetaDescriptions(array $localizedMetaDescriptions): self
    {
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return EditSupplierCommand
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getAssociatedShops(): ?array
    {
        return $this->associatedShops;
    }

    /**
     * @param array $associatedShops
     *
     * @return EditSupplierCommand
     */
    public function setAssociatedShops(array $associatedShops): self
    {
        $this->associatedShops = $associatedShops;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDni(): ?string
    {
        return $this->dni;
    }

    /**
     * @param string $dni
     *
     * @return EditSupplierCommand
     */
    public function setDni(string $dni): self
    {
        $this->dni = $dni;

        return $this;
    }
}
