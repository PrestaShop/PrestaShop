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
        private readonly ?string $externalRef,
        private readonly bool $deliveryAuthorized,
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
}
