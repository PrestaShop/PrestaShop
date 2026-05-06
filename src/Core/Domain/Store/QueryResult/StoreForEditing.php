<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\QueryResult;

/**
 * Carries all data needed to pre-fill the store add/edit form.
 * Translatable fields are arrays keyed by language id.
 */
class StoreForEditing
{
    /**
     * @param array<int, string> $localizedNames
     * @param array<int, string> $localizedAddress1
     * @param array<int, string> $localizedAddress2
     * @param array<int, string[]> $localizedHours JSON-decoded hours per language (7 "HH:MM | HH:MM" strings per lang)
     * @param array<int, string> $localizedNotes
     * @param int[] $shopAssociation
     */
    public function __construct(
        private readonly int $storeId,
        private readonly bool $active,
        private readonly array $localizedNames,
        private readonly array $localizedAddress1,
        private readonly array $localizedAddress2,
        private readonly array $localizedHours,
        private readonly array $localizedNotes,
        private readonly int $countryId,
        private readonly ?int $stateId,
        private readonly string $city,
        private readonly string $postcode,
        private readonly ?float $latitude,
        private readonly ?float $longitude,
        private readonly ?string $phone,
        private readonly ?string $fax,
        private readonly ?string $email,
        private readonly ?array $storeImage,
        private readonly array $shopAssociation,
    ) {
    }

    public function getStoreId(): int
    {
        return $this->storeId;
    }

    public function isActive(): bool
    {
        return $this->active;
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

    public function getStoreImage(): ?array
    {
        return $this->storeImage;
    }

    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }
}
