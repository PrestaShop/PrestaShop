<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult;

use DateTimeImmutable;

class BusinessEntityForViewing
{
    /**
     * @param AddressForViewing[] $invoiceAddresses
     * @param AddressForViewing[] $deliveryAddresses
     * @param IdentifierForViewing[] $identifiers
     */
    public function __construct(
        private readonly int $businessEntityId,
        private readonly ?string $externalRef,
        private readonly string $name,
        private readonly ?string $legalName,
        private readonly bool $deliveryAuthorized,
        private readonly string $status,
        private readonly string $statusLabel,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt,
        private readonly int $linkedCustomersCount,
        private readonly int $addressesCount,
        private readonly int $customerGroupId,
        private readonly string $customerGroupName,
        private readonly array $invoiceAddresses,
        private readonly array $deliveryAddresses,
        private readonly array $identifiers,
    ) {
    }

    public function getBusinessEntityId(): int
    {
        return $this->businessEntityId;
    }

    public function getExternalRef(): ?string
    {
        return $this->externalRef;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Up to two uppercase initials derived from the name, used by the detail page avatar.
     */
    public function getInitials(): string
    {
        $words = array_slice(array_values(array_filter(explode(' ', trim($this->name)))), 0, 2);

        $initials = '';
        foreach ($words as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }

        return $initials;
    }

    public function getLegalName(): ?string
    {
        return $this->legalName;
    }

    public function isDeliveryAuthorized(): bool
    {
        return $this->deliveryAuthorized;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatusLabel(): string
    {
        return $this->statusLabel;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getLinkedCustomersCount(): int
    {
        return $this->linkedCustomersCount;
    }

    public function getCustomerGroupId(): int
    {
        return $this->customerGroupId;
    }

    public function getCustomerGroupName(): string
    {
        return $this->customerGroupName;
    }

    /**
     * @return AddressForViewing[]
     */
    public function getInvoiceAddresses(): array
    {
        return $this->invoiceAddresses;
    }

    /**
     * @return AddressForViewing[]
     */
    public function getDeliveryAddresses(): array
    {
        return $this->deliveryAddresses;
    }

    /**
     * @return IdentifierForViewing[]
     */
    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }

    public function getAddressesCount(): int
    {
        return $this->addressesCount;
    }
}
