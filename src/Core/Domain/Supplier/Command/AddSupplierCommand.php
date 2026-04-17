<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\Command;

/**
 * Creates new supplier with provided data
 */
class AddSupplierCommand
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $city;

    /**
     * @var int|null
     */
    private $countryId;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string[]
     */
    private $localizedDescriptions;

    /**
     * @var string[]
     */
    private $localizedMetaTitles;

    /**
     * @var string[]
     */
    private $localizedMetaDescriptions;

    /**
     * @var int[]
     */
    private $shopAssociation;

    /**
     * @var string|null
     */
    private $address2;

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
     * @var string|null
     */
    private $dni;

    /**
     * @param string $name
     * @param string $address
     * @param string $city
     * @param int $countryId
     * @param bool $enabled
     * @param string[] $localizedDescriptions
     * @param string[] $localizedMetaTitles
     * @param string[] $localizedMetaDescriptions
     * @param array $shopAssociation
     * @param string|null $address2
     * @param string|null $postCode
     * @param int|null $stateId
     * @param string|null $phone
     * @param string|null $mobilePhone
     * @param string $dni
     */
    public function __construct(
        string $name,
        string $address,
        string $city,
        int $countryId,
        bool $enabled,
        array $localizedDescriptions,
        array $localizedMetaTitles,
        array $localizedMetaDescriptions,
        array $shopAssociation,
        ?string $address2 = null,
        ?string $postCode = null,
        ?int $stateId = null,
        ?string $phone = null,
        ?string $mobilePhone = null,
        ?string $dni = null
    ) {
        $this->name = $name;
        $this->address = $address;
        $this->city = $city;
        $this->countryId = $countryId;
        $this->enabled = $enabled;
        $this->localizedDescriptions = $localizedDescriptions;
        $this->localizedMetaTitles = $localizedMetaTitles;
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;
        $this->shopAssociation = $shopAssociation;
        $this->address2 = $address2;
        $this->postCode = $postCode;
        $this->stateId = $stateId;
        $this->phone = $phone;
        $this->mobilePhone = $mobilePhone;
        $this->dni = $dni;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
     * @return string|null
     */
    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * @return int
     */
    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    /**
     * @return string|null
     */
    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    /**
     * @return int|null
     */
    public function getStateId(): ?int
    {
        return $this->stateId;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return string|null
     */
    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions(): array
    {
        return $this->localizedDescriptions;
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
     * @return int[]
     */
    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }

    /**
     * @return string|null
     */
    public function getDni(): ?string
    {
        return $this->dni;
    }
}
