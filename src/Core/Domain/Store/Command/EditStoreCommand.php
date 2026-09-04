<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\Command;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;

/**
 * Edits an existing store. All setters are optional — only provided fields are updated.
 */
class EditStoreCommand
{
    private StoreId $storeId;

    /** @var array<int, string>|null */
    private ?array $localizedNames = null;

    /** @var array<int, string>|null */
    private ?array $localizedAddress1 = null;

    /** @var array<int, string>|null */
    private ?array $localizedAddress2 = null;

    /** @var array<int, string>|null */
    private ?array $localizedHours = null;

    /** @var array<int, string>|null */
    private ?array $localizedNotes = null;

    private ?int $countryId = null;
    private ?int $stateId = null;
    private bool $stateIdProvided = false;
    private ?string $city = null;
    private ?string $postcode = null;
    private ?DecimalNumber $latitude = null;
    private ?DecimalNumber $longitude = null;
    private ?string $phone = null;
    private ?string $fax = null;
    private ?string $email = null;
    private ?bool $active = null;
    private ?string $imagePath = null;

    /** @var int[]|null */
    private ?array $shopAssociation = null;

    public function __construct(int $storeId)
    {
        $this->storeId = new StoreId($storeId);
    }

    public function getStoreId(): StoreId
    {
        return $this->storeId;
    }

    public function getLocalizedNames(): ?array
    {
        return $this->localizedNames;
    }

    public function getLocalizedAddress1(): ?array
    {
        return $this->localizedAddress1;
    }

    public function getLocalizedAddress2(): ?array
    {
        return $this->localizedAddress2;
    }

    public function getLocalizedHours(): ?array
    {
        return $this->localizedHours;
    }

    public function getLocalizedNotes(): ?array
    {
        return $this->localizedNotes;
    }

    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    public function getStateId(): ?int
    {
        return $this->stateId;
    }

    public function isStateIdProvided(): bool
    {
        return $this->stateIdProvided;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function getLatitude(): ?DecimalNumber
    {
        return $this->latitude;
    }

    public function getLongitude(): ?DecimalNumber
    {
        return $this->longitude;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function getShopAssociation(): ?array
    {
        return $this->shopAssociation;
    }

    public function setLocalizedNames(array $localizedNames): self
    {
        $this->localizedNames = $localizedNames;

        return $this;
    }

    public function setLocalizedAddress1(array $localizedAddress1): self
    {
        $this->localizedAddress1 = $localizedAddress1;

        return $this;
    }

    public function setLocalizedAddress2(array $localizedAddress2): self
    {
        $this->localizedAddress2 = $localizedAddress2;

        return $this;
    }

    public function setLocalizedHours(array $localizedHours): self
    {
        $this->localizedHours = $localizedHours;

        return $this;
    }

    public function setLocalizedNotes(array $localizedNotes): self
    {
        $this->localizedNotes = $localizedNotes;

        return $this;
    }

    public function setCountryId(int $countryId): self
    {
        $this->countryId = $countryId;

        return $this;
    }

    public function setStateId(?int $stateId): self
    {
        $this->stateId = $stateId;
        $this->stateIdProvided = true;

        return $this;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function setLatitude(?DecimalNumber $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function setLongitude(?DecimalNumber $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @param string|null $imagePath path of the uploaded image file
     */
    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function setShopAssociation(?array $shopAssociation): self
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }
}
