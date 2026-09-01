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
 * Partial-update command: only fields explicitly set via setters are persisted.
 * A null getter value means "not changed in this request", not "set to null in DB".
 * External reference is the one nullable field that can also be cleared, so it carries its own
 * hasExternalRef() marker to tell "clear it" apart from "leave it alone".
 *
 * Structural fields (shop, business identifiers) are intentionally absent: they are not editable
 * through this command.
 *
 * @see EditBusinessEntityHandlerInterface
 */
class EditBusinessEntityCommand
{
    private readonly BusinessEntityId $businessEntityId;

    private ?string $name = null;

    private ?string $legalName = null;

    private ?string $externalRef = null;

    private bool $externalRefSet = false;

    private ?bool $deliveryAuthorized = null;

    private ?BusinessEntityStatus $status = null;

    private ?int $customerGroupId = null;

    public function __construct(int $businessEntityId)
    {
        $this->businessEntityId = new BusinessEntityId($businessEntityId);
    }

    public function getBusinessEntityId(): BusinessEntityId
    {
        return $this->businessEntityId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLegalName(): ?string
    {
        return $this->legalName;
    }

    public function setLegalName(string $legalName): self
    {
        $this->legalName = $legalName;

        return $this;
    }

    public function getExternalRef(): ?string
    {
        return $this->externalRef;
    }

    public function hasExternalRef(): bool
    {
        return $this->externalRefSet;
    }

    public function setExternalRef(?string $externalRef): self
    {
        $this->externalRef = $externalRef;
        $this->externalRefSet = true;

        return $this;
    }

    public function getDeliveryAuthorized(): ?bool
    {
        return $this->deliveryAuthorized;
    }

    public function setDeliveryAuthorized(bool $deliveryAuthorized): self
    {
        $this->deliveryAuthorized = $deliveryAuthorized;

        return $this;
    }

    public function getStatus(): ?BusinessEntityStatus
    {
        return $this->status;
    }

    public function setStatus(BusinessEntityStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomerGroupId(): ?int
    {
        return $this->customerGroupId;
    }

    public function setCustomerGroupId(int $customerGroupId): self
    {
        $this->customerGroupId = $customerGroupId;

        return $this;
    }
}
