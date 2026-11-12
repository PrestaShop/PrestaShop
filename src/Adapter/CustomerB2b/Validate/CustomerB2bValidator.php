<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerB2b\Validate;

use Customer;
use PrestaShop\PrestaShop\Adapter\Customer\Repository\CustomerRepository;
use PrestaShop\PrestaShop\Core\Domain\B2bRole\Exception\B2bRoleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\B2bRole\ValueObject\B2bRoleId;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Command\AddCustomerB2bCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CannotAddCustomerB2bException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CustomerB2bAlreadyExistsException;
use PrestaShopBundle\Entity\B2B\B2bRole;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Repository\B2bRoleRepository;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use PrestaShopBundle\Entity\Repository\CustomerB2bRepository;
use PrestaShopBundle\Entity\Repository\ShopRepository;
use PrestaShopBundle\Entity\Shop;

/**
 * @experimental
 */
class CustomerB2bValidator
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
        private readonly CustomerB2bRepository $customerB2bRepository,
        private readonly BusinessEntityRepository $businessEntityRepository,
        private readonly B2bRoleRepository $roleRepository,
        private readonly ShopRepository $shopRepository,
    ) {
    }

    /**
     * @throws CustomerNotFoundException
     * @throws CannotAddCustomerB2bException
     * @throws CustomerB2bAlreadyExistsException
     * @throws BusinessEntityNotFoundException
     * @throws B2bRoleNotFoundException
     */
    public function validate(AddCustomerB2bCommand $command): CustomerB2bValidationResult
    {
        $customer = $this->customerRepository->get($command->customerId);
        $this->assertCustomerCanHaveProfile($customer);

        $businessEntity = $this->getBusinessEntity($command->businessEntityId);
        $role = $this->getRole($command->roleId);
        $this->assertCustomerAndBusinessEntityShareShopAssociation($customer, $businessEntity);

        return new CustomerB2bValidationResult($businessEntity, $role);
    }

    /**
     * @throws CannotAddCustomerB2bException
     * @throws CustomerB2bAlreadyExistsException
     */
    private function assertCustomerCanHaveProfile(Customer $customer): void
    {
        $customerId = (int) $customer->id;

        if ($customer->deleted) {
            throw new CannotAddCustomerB2bException(\sprintf('Customer #%d is deleted.', $customerId), CannotAddCustomerB2bException::DELETED_CUSTOMER);
        }

        if ($customer->isGuest()) {
            throw new CannotAddCustomerB2bException(\sprintf('Customer #%d is a guest.', $customerId), CannotAddCustomerB2bException::GUEST_CUSTOMER);
        }

        if (null !== $this->customerB2bRepository->findByCustomerId($customerId)) {
            throw new CustomerB2bAlreadyExistsException(\sprintf('Customer #%d already has a B2B profile.', $customerId));
        }
    }

    /**
     * @throws BusinessEntityNotFoundException
     */
    private function getBusinessEntity(BusinessEntityId $id): BusinessEntity
    {
        if (null !== $businessEntity = $this->businessEntityRepository->findById($id->getValue())) {
            return $businessEntity;
        }

        throw new BusinessEntityNotFoundException(\sprintf('Business entity #%d was not found.', $id->getValue()));
    }

    /**
     * @throws B2bRoleNotFoundException
     */
    private function getRole(B2bRoleId $id): B2bRole
    {
        if (null !== $role = $this->roleRepository->find($id->getValue())) {
            return $role;
        }

        throw new B2bRoleNotFoundException(\sprintf('B2B role #%d was not found.', $id->getValue()));
    }

    /**
     * @throws CannotAddCustomerB2bException
     */
    private function assertCustomerAndBusinessEntityShareShopAssociation(Customer $customer, BusinessEntity $businessEntity): void
    {
        $shopId = $businessEntity->getIdShop();
        if ($shopId === (int) $customer->id_shop) {
            return;
        }

        /** @var Shop|null $shop */
        $shop = $this->shopRepository->find($shopId);
        $shopGroup = $shop?->getShopGroup();

        if ($shopGroup?->getShareCustomer() && $shopGroup->getId() === (int) $customer->id_shop_group) {
            return;
        }

        throw new CannotAddCustomerB2bException(\sprintf('Customer #%d and business entity #%d are associated with different shops.', (int) $customer->id, $businessEntity->getId()), CannotAddCustomerB2bException::SHOP_MISMATCH);
    }
}
