<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command;

use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\CommandHandler\AddBusinessEntityHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityBillingAddressConstraintException;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

/**
 * Class AddBusinessEntityCommand is used to add a new business entity.
 *
 * @see AddBusinessEntityHandlerInterface
 */
class AddBusinessEntityCommand
{
    /**
     * @throws BusinessEntityBillingAddressConstraintException
     */
    public function __construct(
        private readonly string $name,
        private readonly string $legalName,
        private readonly ?string $externalRef,
        private readonly bool $deliveryAuthorized,
        private readonly BusinessEntityStatus $status,
        private readonly int $shopId,
        private readonly int $customerGroupId,
        private readonly bool $billingAddressAsShippingAddress,
        private readonly array $billingAddresses = [],
        private readonly array $shippingAddresses = [],
    ) {
        $this->assertBusinessEntityAddressAreConsistent();
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

    public function getBillingAddresses(): array
    {
        return $this->billingAddresses;
    }

    public function getShippingAddresses(): array
    {
        return $this->shippingAddresses;
    }

    public function isBillingAddressAsShippingAddress(): bool
    {
        return $this->billingAddressAsShippingAddress;
    }

    /**
     * @throws BusinessEntityBillingAddressConstraintException
     */
    protected function assertBusinessEntityAddressAreConsistent(): void
    {
        if (!count($this->getBillingAddresses())) {
            throw new BusinessEntityBillingAddressConstraintException(
                code: BusinessEntityBillingAddressConstraintException::MISSING_BILLING_ADDRESS
            );
        }

        $atLeastOneDefaultBillingAddress = false;
        foreach ($this->getBillingAddresses() as $billingAddress) {
            if ($billingAddress->isDefault()) {
                $atLeastOneDefaultBillingAddress = true;
            }
        }
        if (!$atLeastOneDefaultBillingAddress) {
            throw new BusinessEntityBillingAddressConstraintException(
                code: BusinessEntityBillingAddressConstraintException::MISSING_DEFAULT_BILLING_ADDRESS
            );
        }

        if (!$this->isBillingAddressAsShippingAddress()) {
            if (!count($this->getShippingAddresses())) {
                throw new BusinessEntityBillingAddressConstraintException(
                    code: BusinessEntityBillingAddressConstraintException::MISSING_SHIPPING_ADDRESS
                );
            }

            $atLeastOneDefaultShippingAddress = false;

            foreach ($this->getShippingAddresses() as $shippingAddress) {
                if ($shippingAddress->isDefault()) {
                    $atLeastOneDefaultShippingAddress = true;
                }
            }

            if (!$atLeastOneDefaultShippingAddress) {
                throw new BusinessEntityBillingAddressConstraintException(
                    code: BusinessEntityBillingAddressConstraintException::MISSING_DEFAULT_SHIPPING_ADDRESS
                );
            }
        }
    }
}
