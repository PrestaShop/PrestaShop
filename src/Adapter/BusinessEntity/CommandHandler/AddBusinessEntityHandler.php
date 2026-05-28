<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\BusinessEntity\CommandHandler;

use Address;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PrestaShop\PrestaShop\Adapter\Address\Repository\AddressRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\AddBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\CommandHandler\AddBusinessEntityHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityBillingAddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\UnableToCreateBusinessEntityAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\AbstractBusinessEntityAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityBillingAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\B2B\BusinessEntityAddress;
use PrestaShopBundle\Entity\Enum\AddressTypeEnum;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommandHandler]
final class AddBusinessEntityHandler implements AddBusinessEntityHandlerInterface
{
    /** @var AddressId[] */
    private array $createdAddressId = [];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AddressRepository $addressRepository,
        #[Autowire(service: 'prestashop.adapter.legacy.logger')]
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws BusinessEntityBillingAddressConstraintException
     * @throws UnableToCreateBusinessEntityAddress
     * @throws AddressConstraintException
     */
    public function handle(AddBusinessEntityCommand $command): BusinessEntityId
    {
        $businessEntity = new BusinessEntity();
        $businessEntity->setName($command->getName());
        $businessEntity->setLegalName($command->getLegalName());
        $businessEntity->setExternalRef($command->getExternalRef());
        $businessEntity->setDeliveryAuthorized($command->isDeliveryAuthorized());
        $businessEntity->setStatus($command->getStatus());
        $businessEntity->setIdShop($command->getShopId());
        $businessEntity->setIdCustomerGroup($command->getCustomerGroupId());

        try {
            $this->addAddressesToBusinessEntity($businessEntity, $command);
            $this->em->persist($businessEntity);
            $this->em->flush();
        } catch (Exception $e) {
            if (count($this->createdAddressId)) {
                foreach ($this->createdAddressId as $addressId) {
                    try {
                        $this->addressRepository->delete($addressId);
                    } catch (Exception) {
                    }
                }
            }
            throw new UnableToCreateBusinessEntityAddress(previous: $e);
        }

        $businessEntityId = $businessEntity->getId();

        $this->logger->info(
            'Business entity created successfully',
            [
                'object_type' => 'BusinessEntity',
                'object_id' => $businessEntityId,
            ]
        );

        return new BusinessEntityId($businessEntityId);
    }

    /**
     * @throws CountryConstraintException
     * @throws StateConstraintException
     * @throws UnableToCreateBusinessEntityAddress
     */
    protected function addAddressesToBusinessEntity(BusinessEntity $businessEntity, AddBusinessEntityCommand $command): void
    {
        foreach (array_merge($command->getBillingAddresses(), $command->getShippingAddresses()) as $item) {
            $addressId = $this->addAddressToBusinessEntity($item, $businessEntity);
            $this->createdAddressId[] = $addressId;
            $businessEntityAddress = new BusinessEntityAddress();

            if ($item instanceof BusinessEntityBillingAddress) {
                $addressType = $item->isDefault() && $command->isBillingAddressAsShippingAddress()
                    ? AddressTypeEnum::BOTH : AddressTypeEnum::INVOICE;
            } else {
                $addressType = AddressTypeEnum::DELIVERY;
            }

            $businessEntityAddress
                ->setAddressId($addressId->getValue())
                ->setAddressType($addressType);

            $businessEntityAddress->setIsDefault($item->isDefault());

            $businessEntity->addBusinessEntityAddress($businessEntityAddress);
        }
    }

    /**
     * @throws CountryConstraintException
     * @throws StateConstraintException
     * @throws UnableToCreateBusinessEntityAddress
     */
    protected function addAddressToBusinessEntity(
        AbstractBusinessEntityAddress $address,
        BusinessEntity $businessEntity
    ): AddressId {
        $modelAddress = new Address();
        $modelAddress->id_country = $address->getCountryId()->getValue();
        $modelAddress->alias = $address->getAlias();
        $modelAddress->lastname = 'business-entity';
        $modelAddress->firstname = 'business-entity';
        $modelAddress->address1 = $address->getAddress1();
        $modelAddress->address2 = $address->getAddress2();
        $modelAddress->city = $address->getCity();
        $modelAddress->postcode = $address->getPostCode();
        $modelAddress->id_state = $address->getStateId()->getValue();

        try {
            return $this->addressRepository->add($modelAddress);
        } catch (Exception $e) {
            throw new UnableToCreateBusinessEntityAddress(previous: $e);
        }
    }
}
