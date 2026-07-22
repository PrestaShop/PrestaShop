<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\BusinessEntity\CommandHandler;

use Address;
use Exception;
use PrestaShop\PrestaShop\Adapter\Address\Repository\AddressRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\AddBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\CommandHandler\AddBusinessEntityHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\UnableToCreateBusinessEntityAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\AbstractBusinessEntityAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityBillingAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\B2B\BusinessEntityAddress;
use PrestaShopBundle\Entity\Enum\AddressTypeEnum;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommandHandler]
final class AddBusinessEntityHandler implements AddBusinessEntityHandlerInterface
{
    public function __construct(
        private readonly BusinessEntityRepository $businessEntityRepository,
        private readonly AddressRepository $addressRepository,
        #[Autowire(service: 'prestashop.adapter.legacy.logger')]
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws UnableToCreateBusinessEntityAddress
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

        /** @var AddressId[] $createdAddressIds */
        $createdAddressIds = [];

        // Hybrid persistence: the BusinessEntity aggregate is a Doctrine entity, but addresses are
        // created through the legacy Address ObjectModel (there is no Doctrine Address entity yet, so
        // BusinessEntityAddress only holds a logical id_address int, not a real ORM relation).
        // The two persistence layers do NOT share a transaction: the legacy addresses are written to
        // the database immediately, before the Doctrine flush below. If the flush fails we therefore
        // have to compensate by deleting the orphan addresses ourselves — there is no transactional
        // rollback spanning both layers. This stays until Address gets a Doctrine entity of its own.
        try {
            $this->addAddressesToBusinessEntity($businessEntity, $command, $createdAddressIds);
            $this->businessEntityRepository->save($businessEntity);
        } catch (Exception $e) {
            foreach ($createdAddressIds as $addressId) {
                try {
                    $this->addressRepository->delete($addressId);
                } catch (Exception $cleanupException) {
                    $this->logger->error(
                        'Failed to roll back business entity address after creation failure',
                        [
                            'object_type' => 'Address',
                            'object_id' => $addressId->getValue(),
                            'exception' => $cleanupException,
                        ]
                    );
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
     * @param AddressId[] $createdAddressIds
     *
     * @throws UnableToCreateBusinessEntityAddress
     */
    private function addAddressesToBusinessEntity(BusinessEntity $businessEntity, AddBusinessEntityCommand $command, array &$createdAddressIds): void
    {
        foreach (array_merge($command->getBillingAddresses(), $command->getShippingAddresses()) as $address) {
            $addressId = $this->addAddressToBusinessEntity($address);
            $createdAddressIds[] = $addressId;
            $businessEntityAddress = new BusinessEntityAddress();

            if ($address instanceof BusinessEntityBillingAddress) {
                $addressType = $address->isDefault() && $command->isBillingAddressAsShippingAddress()
                    ? AddressTypeEnum::BOTH : AddressTypeEnum::INVOICE;
            } else {
                $addressType = AddressTypeEnum::DELIVERY;
            }

            $businessEntityAddress
                ->setAddressId($addressId->getValue())
                ->setAddressType($addressType);

            $businessEntityAddress->setIsDefault($address->isDefault());

            $businessEntity->addBusinessEntityAddress($businessEntityAddress);
        }
    }

    /**
     * @throws UnableToCreateBusinessEntityAddress
     */
    private function addAddressToBusinessEntity(
        AbstractBusinessEntityAddress $address
    ): AddressId {
        $modelAddress = new Address();
        $modelAddress->id_country = $address->getCountryId()->getValue();
        $modelAddress->alias = $address->getAlias();
        $modelAddress->lastname = 'business-entity';
        $modelAddress->firstname = 'business-entity';
        $modelAddress->address1 = $address->getAddress1();
        $modelAddress->address2 = $address->getAddress2();
        $modelAddress->city = $address->getCity();
        $modelAddress->postcode = $address->getPostcode();
        $modelAddress->id_state = $address->getStateId()->getValue();
        $modelAddress->phone = $address->getPhone();
        $modelAddress->phone_mobile = $address->getPhoneMobile();

        try {
            return $this->addressRepository->add($modelAddress);
        } catch (Exception $e) {
            throw new UnableToCreateBusinessEntityAddress(previous: $e);
        }
    }
}
