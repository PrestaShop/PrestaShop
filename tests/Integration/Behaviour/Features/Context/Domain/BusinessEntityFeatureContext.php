<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Address;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\AddBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityBillingAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityShippingAddress;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\B2B\BusinessEntityAddress;
use PrestaShopBundle\Entity\Enum\AddressTypeEnum;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use Validate;

class BusinessEntityFeatureContext extends AbstractDomainFeatureContext
{
    private array $businessEntityDetails = [];
    private array $billingAddresses = [];
    private array $shippingAddresses = [];
    private ?BusinessEntityId $lastBusinessEntityId;

    /**
     * @Given there is a business entity with the following details:
     */
    public function thereIsABusinessEntityWithFollowingDetails(TableNode $table)
    {
        $this->businessEntityDetails = $table->getRowsHash();
    }

    /**
     * @Given the business entity has the following billing addresses:
     */
    public function businessEntityHasFollowingBillingAddresses(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $this->billingAddresses[] = new BusinessEntityBillingAddress(
                $row['alias'],
                $row['address1'],
                $row['address2'] ?? null,
                $row['city'],
                $row['postcode'],
                (int) $row['country_id'],
                (bool) $row['is_default'],
                isset($row['state_id']) ? (int) $row['state_id'] : null,
                $row['phone'] ?? null,
                $row['phone_mobile'] ?? null
            );
        }
    }

    /**
     * @Given the business entity has the following shipping addresses:
     */
    public function businessEntityHasFollowingShippingAddresses(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $this->shippingAddresses[] = new BusinessEntityShippingAddress(
                $row['alias'],
                $row['address1'],
                $row['address2'] ?? null,
                $row['city'],
                $row['postcode'],
                (int) $row['country_id'],
                (bool) $row['is_default'],
                isset($row['state_id']) ? (int) $row['state_id'] : null,
                $row['phone'] ?? null,
                $row['phone_mobile'] ?? null
            );
        }
    }

    /**
     * @When I add the business entity
     */
    public function iAddTheBusinessEntity()
    {
        $command = new AddBusinessEntityCommand(
            $this->businessEntityDetails['name'],
            $this->businessEntityDetails['legal_name'],
            $this->businessEntityDetails['external_ref'] ?? null,
            (bool) ($this->businessEntityDetails['delivery_authorized'] ?? true),
            BusinessEntityStatus::from($this->businessEntityDetails['status']),
            (int) ($this->businessEntityDetails['shop_id'] ?? 1),
            (int) ($this->businessEntityDetails['customer_group_id'] ?? 3),
            (bool) ($this->businessEntityDetails['billing_as_shipping'] ?? false),
            $this->billingAddresses,
            $this->shippingAddresses
        );

        try {
            $this->lastBusinessEntityId = $this->getCommandBus()->handle($command);
        } catch (BusinessEntityException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then the business entity should be successfully created
     */
    public function businessEntityShouldBeSuccessfullyCreated()
    {
        Assert::assertNotNull($this->lastBusinessEntityId, 'Business entity was not created');
    }

    /**
     * @Then the business entity :name should exist in the database
     */
    public function businessEntityShouldExistInDatabase(string $name)
    {
        // TODO: use CQRS when it will be available
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(BusinessEntity::class);

        /** @var BusinessEntity|null $businessEntity */
        $businessEntity = $repository->findOneBy(['name' => $name]);

        Assert::assertNotNull($businessEntity, sprintf('Business entity with name "%s" not found in database', $name));
        Assert::assertEquals($name, $businessEntity->getName());

        if (isset($this->businessEntityDetails['legal_name'])) {
            Assert::assertEquals($this->businessEntityDetails['legal_name'], $businessEntity->getLegalName());
        }
    }

    /**
     * @Then the business entity :name should belong to customer group :groupId
     */
    public function businessEntityShouldBelongToCustomerGroup(string $name, int $groupId)
    {
        $businessEntity = $this->getBusinessEntityByName($name);
        Assert::assertSame(
            $groupId,
            $businessEntity->getIdCustomerGroup(),
            sprintf('Business entity "%s" customer group mismatch', $name)
        );
    }

    /**
     * @Then the business entity :name should be attached to shop :shopId
     */
    public function businessEntityShouldBeAttachedToShop(string $name, int $shopId)
    {
        $businessEntity = $this->getBusinessEntityByName($name);
        Assert::assertSame(
            $shopId,
            $businessEntity->getIdShop(),
            sprintf('Business entity "%s" shop mismatch', $name)
        );
    }

    /**
     * @Then the business entity :name should have :count address(es)
     */
    public function businessEntityShouldHaveCountAddresses(string $name, int $count)
    {
        $businessEntity = $this->getBusinessEntityByName($name);
        Assert::assertCount($count, $businessEntity->getBusinessEntityAddresses());
    }

    /**
     * @Then the address with alias :alias for business entity :name should have type :type
     */
    public function addressWithAliasShouldHaveType(string $alias, string $name, string $type)
    {
        $businessEntityAddress = $this->getBusinessEntityAddressByAlias($name, $alias);
        Assert::assertEquals(AddressTypeEnum::from($type), $businessEntityAddress->getAddressType());
    }

    /**
     * @Then the address with alias :alias for business entity :name should have type :type and be default
     */
    public function addressWithAliasShouldHaveTypeAndBeDefault(string $alias, string $name, string $type)
    {
        $businessEntityAddress = $this->getBusinessEntityAddressByAlias($name, $alias);
        Assert::assertEquals(AddressTypeEnum::from($type), $businessEntityAddress->getAddressType());
        Assert::assertTrue($businessEntityAddress->isDefault());
    }

    /**
     * @Then the address with alias :alias for business entity :name should have phone :phone and mobile phone :mobile
     */
    public function addressWithAliasShouldHavePhones(string $alias, string $name, string $phone, string $mobile): void
    {
        $businessEntityAddress = $this->getBusinessEntityAddressByAlias($name, $alias);
        $address = new Address($businessEntityAddress->getAddressId());
        Assert::assertEquals($phone, $address->phone);
        Assert::assertEquals($mobile, $address->phone_mobile);
    }

    private function getBusinessEntityByName(string $name): BusinessEntity
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(BusinessEntity::class);

        /** @var BusinessEntity|null $businessEntity */
        $businessEntity = $repository->findOneBy(['name' => $name]);

        Assert::assertNotNull($businessEntity, sprintf('Business entity with name "%s" not found', $name));

        return $businessEntity;
    }

    private function getBusinessEntityAddressByAlias(string $businessEntityName, string $alias): BusinessEntityAddress
    {
        $businessEntity = $this->getBusinessEntityByName($businessEntityName);

        foreach ($businessEntity->getBusinessEntityAddresses() as $businessEntityAddress) {
            /** @var BusinessEntityAddress $businessEntityAddress */
            $address = new Address($businessEntityAddress->getAddressId());
            if (Validate::isLoadedObject($address) && $address->alias === $alias) {
                return $businessEntityAddress;
            }
        }

        Assert::fail(sprintf('Address with alias "%s" not found for business entity "%s"', $alias, $businessEntityName));
    }
}
