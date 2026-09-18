<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Address;
use Behat\Gherkin\Node\TableNode;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\AddBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\EditBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetBusinessEntityForEditing;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetBusinessEntityForViewing;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetPendingBusinessEntitiesCount;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\AddressForViewing;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\BusinessEntityForViewing;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\EditableBusinessEntity;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityBillingAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityShippingAddress;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\B2B\B2bRole;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\B2B\BusinessEntityAddress;
use PrestaShopBundle\Entity\B2B\BusinessEntityCustomerB2b;
use PrestaShopBundle\Entity\B2B\CustomerB2b;
use PrestaShopBundle\Entity\Enum\AddressTypeEnum;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Entity\Enum\CustomerB2bStatus;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Validate;

class BusinessEntityFeatureContext extends AbstractDomainFeatureContext
{
    private const DEFAULT_SHOP_ID = 1;
    private const DEFAULT_CUSTOMER_GROUP_ID = 3;
    private const DEFAULT_COUNTRY_ID = 8;

    /**
     * Offset kept above the fixture customers so each linked CustomerB2b gets a distinct
     * id_customer (unique index) without colliding with them.
     */
    private const FIRST_LINKED_CUSTOMER_ID = 1000;

    private array $businessEntityDetails = [];
    private array $billingAddresses = [];
    private array $shippingAddresses = [];
    private ?BusinessEntityId $lastBusinessEntityId;
    private int $linkedCustomerSequence = 0;

    /**
     * @BeforeScenario
     */
    public function setUpDefaultShopContext(): void
    {
        /** @var ShopContextBuilder $shopContextBuilder */
        $shopContextBuilder = CommonFeatureContext::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop(self::DEFAULT_SHOP_ID));
        $shopContextBuilder->setShopId(self::DEFAULT_SHOP_ID);
    }

    /**
     * @Given there is a business entity with the following details:
     */
    public function thereIsABusinessEntityWithFollowingDetails(TableNode $table): void
    {
        $this->businessEntityDetails = $table->getRowsHash();
    }

    /**
     * @Given the business entity has the following billing addresses:
     */
    public function businessEntityHasFollowingBillingAddresses(TableNode $table): void
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
    public function businessEntityHasFollowingShippingAddresses(TableNode $table): void
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
    public function iAddTheBusinessEntity(): void
    {
        $command = new AddBusinessEntityCommand(
            $this->businessEntityDetails['name'],
            $this->businessEntityDetails['legal_name'],
            $this->businessEntityDetails['external_ref'] ?? null,
            (bool) ($this->businessEntityDetails['delivery_authorized'] ?? true),
            BusinessEntityStatus::from($this->businessEntityDetails['status']),
            (int) ($this->businessEntityDetails['shop_id'] ?? self::DEFAULT_SHOP_ID),
            (int) ($this->businessEntityDetails['customer_group_id'] ?? self::DEFAULT_CUSTOMER_GROUP_ID),
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
    public function businessEntityShouldBeSuccessfullyCreated(): void
    {
        Assert::assertNotNull($this->lastBusinessEntityId, 'Business entity was not created');
    }

    /**
     * @Then the business entity :name should exist in the database
     */
    public function businessEntityShouldExistInDatabase(string $name): void
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
    public function businessEntityShouldBelongToCustomerGroup(string $name, int $groupId): void
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
    public function businessEntityShouldBeAttachedToShop(string $name, int $shopId): void
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
    public function businessEntityShouldHaveCountAddresses(string $name, int $count): void
    {
        $businessEntity = $this->getBusinessEntityByName($name);
        Assert::assertCount($count, $businessEntity->getBusinessEntityAddresses());
    }

    /**
     * @Then the address with alias :alias for business entity :name should have type :type
     */
    public function addressWithAliasShouldHaveType(string $alias, string $name, string $type): void
    {
        $businessEntityAddress = $this->getBusinessEntityAddressByAlias($name, $alias);
        Assert::assertEquals(AddressTypeEnum::from($type), $businessEntityAddress->getAddressType());
    }

    /**
     * @Then the address with alias :alias for business entity :name should have type :type and be default
     */
    public function addressWithAliasShouldHaveTypeAndBeDefault(string $alias, string $name, string $type): void
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

    /**
     * @When the address with alias :alias for business entity :name is soft deleted
     */
    public function softDeleteAddressWithAlias(string $alias, string $name): void
    {
        $businessEntityAddress = $this->getBusinessEntityAddressByAlias($name, $alias);

        $address = new Address($businessEntityAddress->getAddressId());
        $address->deleted = true;
        $address->save();
    }

    /**
     * @When I view the business entity with id :businessEntityId
     */
    public function iViewTheBusinessEntityWithId(int $businessEntityId): void
    {
        try {
            $this->getQueryBus()->handle(new GetBusinessEntityForViewing($businessEntityId));
        } catch (BusinessEntityException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then the business entity :name should have the following view properties:
     */
    public function businessEntityShouldHaveViewProperties(string $name, TableNode $tableNode): void
    {
        $viewed = $this->getBusinessEntityForViewingByName($name);
        $data = $tableNode->getRowsHash();

        if (isset($data['name'])) {
            Assert::assertSame($data['name'], $viewed->getName());
        }
        if (isset($data['legal_name'])) {
            Assert::assertSame($data['legal_name'], $viewed->getLegalName());
        }
        if (isset($data['status'])) {
            Assert::assertSame(BusinessEntityStatus::from($data['status']), $viewed->getStatus());
        }
        if (isset($data['linked_customers_count'])) {
            Assert::assertSame((int) $data['linked_customers_count'], $viewed->getLinkedCustomersCount());
        }
        if (isset($data['addresses_count'])) {
            Assert::assertSame((int) $data['addresses_count'], $viewed->getAddressesCount());
        }
        if (isset($data['customer_group_name'])) {
            Assert::assertSame($data['customer_group_name'], $viewed->getCustomerGroupName());
        }
    }

    /**
     * @Then the business entity :name should have created and updated timestamps
     */
    public function businessEntityShouldHaveTimestamps(string $name): void
    {
        $viewed = $this->getBusinessEntityForViewingByName($name);

        // The entity was just created by the scenario, so both timestamps must carry a real recent
        // date and not merely look like one.
        Assert::assertGreaterThan(new DateTimeImmutable('-1 hour'), $viewed->getCreatedAt());
        Assert::assertLessThanOrEqual(new DateTimeImmutable('+1 minute'), $viewed->getCreatedAt());
        Assert::assertGreaterThanOrEqual($viewed->getCreatedAt(), $viewed->getUpdatedAt());
    }

    /**
     * @Then the business entity :name should have :count billing address(es)
     */
    public function businessEntityShouldHaveBillingAddresses(string $name, int $count): void
    {
        $viewed = $this->getBusinessEntityForViewingByName($name);
        Assert::assertCount($count, $viewed->getInvoiceAddresses());
    }

    /**
     * @Then the business entity :name should have :count shipping address(es)
     */
    public function businessEntityShouldHaveShippingAddresses(string $name, int $count): void
    {
        $viewed = $this->getBusinessEntityForViewingByName($name);
        Assert::assertCount($count, $viewed->getDeliveryAddresses());
    }

    /**
     * @Then the business entity :name :type address :alias formatted address should contain :value
     */
    public function businessEntityAddressFormattedAddressShouldContain(string $name, string $type, string $alias, string $value): void
    {
        Assert::assertStringContainsString($value, $this->getFormattedAddress($name, $type, $alias));
    }

    /**
     * The formatted address must never expose the placeholder the creation handler stores in the
     * legacy firstname/lastname columns: it is what the formatter's "avoid" rules exist for.
     *
     * @Then the business entity :name :type address :alias formatted address should not contain :value
     */
    public function businessEntityAddressFormattedAddressShouldNotContain(string $name, string $type, string $alias, string $value): void
    {
        Assert::assertStringNotContainsString($value, $this->getFormattedAddress($name, $type, $alias));
    }

    /**
     * @Then the first :type address of business entity :name should be :alias
     */
    public function firstAddressOfTypeShouldBe(string $type, string $name, string $alias): void
    {
        $addresses = $this->getViewedAddressesByType($name, $type);

        Assert::assertNotEmpty($addresses, sprintf('Business entity "%s" has no %s address', $name, $type));
        Assert::assertSame(
            $alias,
            $addresses[0]->getAlias(),
            sprintf('The default %s address of business entity "%s" is not listed first', $type, $name)
        );
    }

    private function getFormattedAddress(string $name, string $type, string $alias): string
    {
        foreach ($this->getViewedAddressesByType($name, $type) as $address) {
            if ($address->getAlias() === $alias) {
                return $address->getFormattedAddress();
            }
        }

        Assert::fail(sprintf('No %s address with alias "%s" for business entity "%s"', $type, $alias, $name));
    }

    /**
     * @return AddressForViewing[]
     */
    private function getViewedAddressesByType(string $name, string $type): array
    {
        $viewed = $this->getBusinessEntityForViewingByName($name);

        return AddressTypeEnum::DELIVERY->value === $type
            ? $viewed->getDeliveryAddresses()
            : $viewed->getInvoiceAddresses();
    }

    /**
     * @Then I should get an error that the business entity was not found
     */
    public function assertLastErrorIsBusinessEntityNotFound(): void
    {
        $this->assertLastErrorIs(BusinessEntityNotFoundException::class);
    }

    /**
     * @Given there is a business entity named :name with status :status
     */
    public function thereIsABusinessEntityNamedWithStatus(string $name, string $status): void
    {
        $command = new AddBusinessEntityCommand(
            $name,
            $name . ' Legal',
            null,
            true,
            BusinessEntityStatus::from($status),
            self::DEFAULT_SHOP_ID,
            self::DEFAULT_CUSTOMER_GROUP_ID,
            true,
            [new BusinessEntityBillingAddress('Billing', '1 Main St', null, 'Paris', '75001', self::DEFAULT_COUNTRY_ID, true, null)],
            []
        );

        $this->getCommandBus()->handle($command);
    }

    /**
     * @Given the business entity :name is linked to :count b2b customers
     */
    public function businessEntityIsLinkedToB2bCustomers(string $name, int $count): void
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $businessEntity = $this->getBusinessEntityByName($name);

        $role = new B2bRole();
        $role->setRole(sprintf('behat-member-%d-%d', $businessEntity->getId(), ++$this->linkedCustomerSequence));
        $entityManager->persist($role);

        for ($i = 1; $i <= $count; ++$i) {
            $customerB2b = new CustomerB2b();
            // ps_customer_b2b carries a unique index on id_customer and the tables are only restored
            // @BeforeFeature, so the ids have to stay distinct across every call of this step —
            // including two calls on the same business entity.
            $customerB2b->setIdCustomer(self::FIRST_LINKED_CUSTOMER_ID + ++$this->linkedCustomerSequence);
            $customerB2b->setStatus(CustomerB2bStatus::ACTIVE);
            $customerB2b->setCreatedAt(new DateTime());
            $customerB2b->setUpdatedAt(new DateTime());
            $entityManager->persist($customerB2b);

            $link = new BusinessEntityCustomerB2b();
            $link->setBusinessEntity($businessEntity);
            $link->setCustomerB2b($customerB2b);
            $link->setB2bRole($role);
            $link->setCreatedAt(new DateTime());
            $link->setUpdatedAt(new DateTime());
            $entityManager->persist($link);
        }

        $entityManager->flush();
    }

    /**
     * @Then the pending business entities count should be :count
     */
    public function thePendingBusinessEntitiesCountShouldBe(int $count): void
    {
        $actual = $this->getQueryBus()->handle(new GetPendingBusinessEntitiesCount());
        Assert::assertSame($count, $actual);
    }

    private function getBusinessEntityForViewingByName(string $name): BusinessEntityForViewing
    {
        $businessEntityId = $this->getBusinessEntityByName($name)->getId();

        return $this->getQueryBus()->handle(new GetBusinessEntityForViewing($businessEntityId));
    }

    /**
     * @When I edit the business entity :name with the following details:
     */
    public function iEditTheBusinessEntity(string $name, TableNode $table): void
    {
        $businessEntity = $this->getBusinessEntityByName($name);
        $data = $table->getRowsHash();

        $command = new EditBusinessEntityCommand($businessEntity->getId());

        if (isset($data['name'])) {
            $command->setName($data['name']);
        }

        if (isset($data['legal_name'])) {
            $command->setLegalName($data['legal_name']);
        }

        if (isset($data['external_ref'])) {
            $command->setExternalRef('' === $data['external_ref'] ? null : $data['external_ref']);
        }

        if (isset($data['delivery_authorized'])) {
            $command->setDeliveryAuthorized(PrimitiveUtils::castStringBooleanIntoBoolean($data['delivery_authorized']));
        }

        if (isset($data['status'])) {
            $command->setStatus(BusinessEntityStatus::from($data['status']));
        }

        if (isset($data['customer_group_id'])) {
            $command->setCustomerGroupId((int) $data['customer_group_id']);
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (BusinessEntityException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then the business entity :name should have the following details:
     */
    public function businessEntityShouldHaveDetails(string $name, TableNode $table): void
    {
        // Re-read from the database, not from the identity map: without this the assertions would
        // pass on the in-memory entity even if nothing had been flushed.
        $this->getContainer()->get('doctrine.orm.entity_manager')->clear();

        $businessEntity = $this->getBusinessEntityByName($name);
        $data = $table->getRowsHash();

        if (isset($data['name'])) {
            Assert::assertSame($data['name'], $businessEntity->getName());
        }
        if (isset($data['legal_name'])) {
            Assert::assertSame($data['legal_name'], $businessEntity->getLegalName());
        }
        if (isset($data['external_ref'])) {
            // Mirrors the input step: an empty cell means "cleared", i.e. NULL, not an empty string.
            Assert::assertSame('' === $data['external_ref'] ? null : $data['external_ref'], $businessEntity->getExternalRef());
        }
        if (isset($data['delivery_authorized'])) {
            Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($data['delivery_authorized']), $businessEntity->isDeliveryAuthorized());
        }
        if (isset($data['status'])) {
            Assert::assertSame(BusinessEntityStatus::from($data['status']), $businessEntity->getStatus());
        }
        if (isset($data['customer_group_id'])) {
            Assert::assertSame((int) $data['customer_group_id'], $businessEntity->getIdCustomerGroup());
        }
    }

    /**
     * @When I edit the business entity with id :businessEntityId
     */
    public function iEditTheBusinessEntityWithId(int $businessEntityId): void
    {
        try {
            $this->getCommandBus()->handle(
                (new EditBusinessEntityCommand($businessEntityId))->setName('Missing')
            );
        } catch (BusinessEntityException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then the business entity :name should be editable with the following details:
     */
    public function businessEntityShouldBeEditableWithDetails(string $name, TableNode $table): void
    {
        $businessEntityId = $this->getBusinessEntityByName($name)->getId();
        $data = $table->getRowsHash();

        /** @var EditableBusinessEntity $editableBusinessEntity */
        $editableBusinessEntity = $this->getQueryBus()->handle(new GetBusinessEntityForEditing($businessEntityId));

        Assert::assertSame($name, $editableBusinessEntity->getName());

        if (isset($data['legal_name'])) {
            Assert::assertSame($data['legal_name'], $editableBusinessEntity->getLegalName());
        }
        if (isset($data['external_ref'])) {
            Assert::assertSame('' === $data['external_ref'] ? null : $data['external_ref'], $editableBusinessEntity->getExternalRef());
        }
        if (isset($data['delivery_authorized'])) {
            Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($data['delivery_authorized']), $editableBusinessEntity->isDeliveryAuthorized());
        }
        if (isset($data['status'])) {
            Assert::assertSame(BusinessEntityStatus::from($data['status']), $editableBusinessEntity->getStatus());
        }
        if (isset($data['customer_group_id'])) {
            Assert::assertSame((int) $data['customer_group_id'], $editableBusinessEntity->getCustomerGroupId());
        }
    }

    /**
     * Back-dates updated_at through DQL so the refresh performed by the edit becomes measurable:
     * the column has second precision, so creating and editing within the same second would make a
     * comparison against created_at non-deterministic. DQL also bypasses the PreUpdate callback,
     * which a plain flush would let overwrite the back-dated value.
     *
     * @Given the business entity :name was last updated an hour ago
     */
    public function businessEntityWasLastUpdatedAnHourAgo(string $name): void
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $entityManager
            ->createQuery(sprintf('UPDATE %s be SET be.updatedAt = :updatedAt WHERE be.id = :id', BusinessEntity::class))
            ->setParameter('updatedAt', new DateTime('-1 hour'))
            ->setParameter('id', $this->getBusinessEntityByName($name)->getId())
            ->execute();

        $entityManager->clear();
    }

    /**
     * @Then the business entity :name should have a refreshed updated_at
     */
    public function businessEntityShouldHaveRefreshedUpdatedAt(string $name): void
    {
        $this->getContainer()->get('doctrine.orm.entity_manager')->clear();

        Assert::assertGreaterThan(
            new DateTime('-1 minute'),
            $this->getBusinessEntityByName($name)->getUpdatedAt(),
            'The updated_at timestamp was not refreshed by the edit.'
        );
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
