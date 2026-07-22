<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command;

use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\CommandHandler\AddBusinessEntityHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityBillingAddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\AbstractBusinessEntityAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityBillingAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityShippingAddress;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

/**
 * Class AddBusinessEntityCommand is used to add a new business entity.
 *
 * @see AddBusinessEntityHandlerInterface
 */
class AddBusinessEntityCommand
{
    /**
     * @param array<BusinessEntityBillingAddress> $billingAddresses
     * @param array<BusinessEntityShippingAddress> $shippingAddresses
     *
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

    /**
     * @return array<BusinessEntityBillingAddress>
     */
    public function getBillingAddresses(): array
    {
        return $this->billingAddresses;
    }

    /**
     * @return array<BusinessEntityShippingAddress>
     */
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
    private function assertBusinessEntityAddressAreConsistent(): void
    {
        if (!count($this->getBillingAddresses())) {
            throw new BusinessEntityBillingAddressConstraintException(
                code: BusinessEntityBillingAddressConstraintException::MISSING_BILLING_ADDRESS
            );
        }

        $defaultBillingAddressCount = $this->countDefaultAddresses($this->getBillingAddresses());
        if (0 === $defaultBillingAddressCount) {
            throw new BusinessEntityBillingAddressConstraintException(
                code: BusinessEntityBillingAddressConstraintException::MISSING_DEFAULT_BILLING_ADDRESS
            );
        }
        if ($defaultBillingAddressCount > 1) {
            throw new BusinessEntityBillingAddressConstraintException(
                code: BusinessEntityBillingAddressConstraintException::MULTIPLE_DEFAULT_BILLING_ADDRESSES
            );
        }

        if (!$this->isBillingAddressAsShippingAddress()) {
            if (!count($this->getShippingAddresses())) {
                throw new BusinessEntityBillingAddressConstraintException(
                    code: BusinessEntityBillingAddressConstraintException::MISSING_SHIPPING_ADDRESS
                );
            }

            $defaultShippingAddressCount = $this->countDefaultAddresses($this->getShippingAddresses());
            if (0 === $defaultShippingAddressCount) {
                throw new BusinessEntityBillingAddressConstraintException(
                    code: BusinessEntityBillingAddressConstraintException::MISSING_DEFAULT_SHIPPING_ADDRESS
                );
            }
            if ($defaultShippingAddressCount > 1) {
                throw new BusinessEntityBillingAddressConstraintException(
                    code: BusinessEntityBillingAddressConstraintException::MULTIPLE_DEFAULT_SHIPPING_ADDRESSES
                );
            }
        }
    }

    /**
     * @param array<AbstractBusinessEntityAddress> $addresses
     */
    private function countDefaultAddresses(array $addresses): int
    {
        $count = 0;
        foreach ($addresses as $address) {
            if ($address->isDefault()) {
                ++$count;
            }
        }

        return $count;
    }
}
