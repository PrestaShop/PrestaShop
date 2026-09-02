<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Transfers supplier data for editing
 */
class EditableSupplier
{
    /**
     * @var SupplierId
     */
    private $supplierId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $localizedDescriptions;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $address2;

    /**
     * @var int
     */
    private $countryId;

    /**
     * @var string
     */
    private $postCode;

    /**
     * @var int
     */
    private $stateId;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $mobilePhone;

    /**
     * @var array
     */
    private $logoImage;

    /**
     * @var string[]
     */
    private $localizedMetaTitles;

    /**
     * @var string[]
     */
    private $localizedMetaDescriptions;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var array
     */
    private $associatedShops;

    /**
     * @var string
     */
    private $dni;

    /**
     * @param SupplierId $supplierId
     * @param string $name
     * @param string[] $localizedDescriptions
     * @param string $address
     * @param string $city
     * @param string $address2
     * @param int $countryId
     * @param string $postCode
     * @param int $stateId
     * @param string $phone
     * @param string $mobilePhone
     * @param string[] $localizedMetaTitles
     * @param string[] $localizedMetaDescriptions
     * @param bool $enabled
     * @param array $associatedShops
     * @param string $dni
     * @param array|null $logoImage
     */
    public function __construct(
        SupplierId $supplierId,
        string $name,
        array $localizedDescriptions,
        string $address,
        string $city,
        string $address2,
        int $countryId,
        string $postCode,
        int $stateId,
        string $phone,
        string $mobilePhone,
        array $localizedMetaTitles,
        array $localizedMetaDescriptions,
        bool $enabled,
        array $associatedShops,
        string $dni,
        ?array $logoImage = null
    ) {
        $this->supplierId = $supplierId;
        $this->name = $name;
        $this->localizedDescriptions = $localizedDescriptions;
        $this->address = $address;
        $this->city = $city;
        $this->address2 = $address2;
        $this->countryId = $countryId;
        $this->postCode = $postCode;
        $this->stateId = $stateId;
        $this->phone = $phone;
        $this->mobilePhone = $mobilePhone;
        $this->logoImage = $logoImage;
        $this->localizedMetaTitles = $localizedMetaTitles;
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;
        $this->enabled = $enabled;
        $this->dni = $dni;
        $this->associatedShops = $associatedShops;
    }

    /**
     * @return SupplierId
     */
    public function getSupplierId(): SupplierId
    {
        return $this->supplierId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions(): array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getAddress2(): string
    {
        return $this->address2;
    }

    /**
     * @return int
     */
    public function getCountryId(): int
    {
        return $this->countryId;
    }

    /**
     * @return string
     */
    public function getPostCode(): string
    {
        return $this->postCode;
    }

    /**
     * @return int
     */
    public function getStateId(): int
    {
        return $this->stateId;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getMobilePhone(): string
    {
        return $this->mobilePhone;
    }

    /**
     * @return array|null
     */
    public function getLogoImage(): ?array
    {
        return $this->logoImage;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaTitles(): array
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaDescriptions(): array
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return array
     */
    public function getAssociatedShops(): array
    {
        return $this->associatedShops;
    }

    /**
     * @return string
     */
    public function getDni(): string
    {
        return $this->dni;
    }
}
