<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\Command;

use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;

/**
 * Creates a new store.
 * All translatable fields are keyed by language id.
 */
class AddStoreCommand
{
    /** @var array<int, string> */
    private array $localizedNames;

    /** @var array<int, string> */
    private array $localizedAddress1;

    /** @var array<int, string> */
    private array $localizedAddress2;

    /** @var array<int, string> */
    private array $localizedHours;

    /** @var array<int, string> */
    private array $localizedNotes;

    private int $countryId;
    private ?int $stateId;
    private string $city;
    private string $postcode;
    private ?float $latitude;
    private ?float $longitude;
    private ?string $phone;
    private ?string $fax;
    private ?string $email;
    private bool $active;

    /** @var int[]|null */
    private ?array $shopAssociation;

    /**
     * @param array<int, string> $localizedNames
     * @param array<int, string> $localizedAddress1
     * @param int $countryId
     * @param string $city
     */
    public function __construct(
        array $localizedNames,
        array $localizedAddress1,
        int $countryId,
        string $city
    ) {
        $this->localizedNames = $localizedNames;
        $this->localizedAddress1 = $localizedAddress1;
        $this->localizedAddress2 = [];
        $this->localizedHours = [];
        $this->localizedNotes = [];
        $this->countryId = $countryId;
        $this->stateId = null;
        $this->city = $city;
        $this->postcode = '';
        $this->latitude = null;
        $this->longitude = null;
        $this->phone = null;
        $this->fax = null;
        $this->email = null;
        $this->active = true;
        $this->shopAssociation = null;
    }

    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    public function getLocalizedAddress1(): array
    {
        return $this->localizedAddress1;
    }

    public function getLocalizedAddress2(): array
    {
        return $this->localizedAddress2;
    }

    public function getLocalizedHours(): array
    {
        return $this->localizedHours;
    }

    public function getLocalizedNotes(): array
    {
        return $this->localizedNotes;
    }

    public function getCountryId(): int
    {
        return $this->countryId;
    }

    public function getStateId(): ?int
    {
        return $this->stateId;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
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

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getShopAssociation(): ?array
    {
        return $this->shopAssociation;
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

    public function setStateId(?int $stateId): self
    {
        $this->stateId = $stateId;

        return $this;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function setLongitude(?float $longitude): self
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

    public function setShopAssociation(?array $shopAssociation): self
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }
}
