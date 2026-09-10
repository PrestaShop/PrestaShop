<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult;

use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

/**
 * Transfers the editable general-information fields of a business entity to the edit form.
 */
class EditableBusinessEntity
{
    public function __construct(
        private readonly int $businessEntityId,
        private readonly string $name,
        private readonly ?string $legalName,
        private readonly ?string $externalRef,
        private readonly bool $deliveryAuthorized,
        private readonly BusinessEntityStatus $status,
        private readonly int $customerGroupId,
        private readonly int $shopId,
    ) {
    }

    public function getBusinessEntityId(): int
    {
        return $this->businessEntityId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLegalName(): ?string
    {
        return $this->legalName;
    }

    public function getExternalRef(): ?string
    {
        return $this->externalRef;
    }

    public function isDeliveryAuthorized(): bool
    {
        return $this->deliveryAuthorized;
    }

    public function getStatus(): BusinessEntityStatus
    {
        return $this->status;
    }

    public function getCustomerGroupId(): int
    {
        return $this->customerGroupId;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }
}
