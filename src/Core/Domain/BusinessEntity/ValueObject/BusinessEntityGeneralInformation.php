<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject;

use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

class BusinessEntityGeneralInformation
{
    public function __construct(
        private readonly string $name,
        private readonly string $legalName,
        private readonly ?string $externalRef,
        private readonly bool $deliveryAuthorized,
        private readonly BusinessEntityStatus $status,
        private readonly int $shopId,
        private readonly int $customerGroupId,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLegalName(): string
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

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getCustomerGroupId(): int
    {
        return $this->customerGroupId;
    }
}
