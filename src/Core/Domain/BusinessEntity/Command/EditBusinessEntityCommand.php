<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command;

use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\CommandHandler\EditBusinessEntityHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

/**
 * Class EditBusinessEntityCommand is used to edit the general information of an existing business entity.
 *
 * @see EditBusinessEntityHandlerInterface
 */
class EditBusinessEntityCommand
{
    private readonly BusinessEntityId $businessEntityId;

    public function __construct(
        int $businessEntityId,
        private readonly string $name,
        private readonly string $legalName,
        private readonly ?string $externalRef,
        private readonly bool $deliveryAuthorized,
        private readonly BusinessEntityStatus $status,
        private readonly int $customerGroupId,
    ) {
        $this->businessEntityId = new BusinessEntityId($businessEntityId);
    }

    public function getBusinessEntityId(): BusinessEntityId
    {
        return $this->businessEntityId;
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

    public function getCustomerGroupId(): int
    {
        return $this->customerGroupId;
    }
}
