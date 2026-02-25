<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject;

use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

class BusinessEntityGeneralInformation
{
    public function __construct(
        private readonly string $name,
        private readonly string $legalName,
        private readonly ?string $enterpriseId,
        private readonly string $externalRef,
        private readonly bool $flagDeliveryAuthorized,
        private readonly BusinessEntityStatus $status
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

    public function getEnterpriseId(): ?string
    {
        return $this->enterpriseId;
    }

    public function getExternalRef(): string
    {
        return $this->externalRef;
    }

    public function isFlagDeliveryAuthorized(): bool
    {
        return $this->flagDeliveryAuthorized;
    }

    public function getStatus(): BusinessEntityStatus
    {
        return $this->status;
    }
}
