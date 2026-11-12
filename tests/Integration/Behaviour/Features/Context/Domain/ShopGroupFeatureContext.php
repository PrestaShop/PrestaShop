<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Context\Employee;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\AddShopGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\DeleteShopGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\EditShopGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotDeleteShopGroupException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotUpdateShopGroupException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopTree;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\EditableShopGroup;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Tests\Resources\Context\EmployeeContextDecorator;

class ShopGroupFeatureContext extends AbstractDomainFeatureContext
{
    private const NON_EXISTING_SHOP_GROUP_ID = 74099901;
    private const NON_SUPER_ADMIN_PROFILE_ID = 2;

    /**
     * @AfterScenario
     */
    public function resetEmployeeContext(): void
    {
        $this->getEmployeeContextDecorator()->resetOverriddenEmployee();
    }

    /**
     * @Given shop group :reference does not exist
     */
    public function setNonExistingShopGroupReference(string $reference): void
    {
        $this->getSharedStorage()->set($reference, self::NON_EXISTING_SHOP_GROUP_ID);
    }

    /**
     * @When I add a shop group :reference with the following properties:
     */
    public function addShopGroup(string $reference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        try {
            /** @var ShopGroupId $shopGroupId */
            $shopGroupId = $this->getCommandBus()->handle(new AddShopGroupCommand(
                $data['name'],
                $data['color'] ?? '',
                PrimitiveUtils::castStringBooleanIntoBoolean($data['share_customer'] ?? 'false'),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['share_stock'] ?? 'false'),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['share_order'] ?? 'false'),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['active'] ?? 'true'),
            ));
            $this->getSharedStorage()->set($reference, $shopGroupId->getValue());
        } catch (ShopException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit shop group :reference with the following properties:
     */
    public function editShopGroup(string $reference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $command = new EditShopGroupCommand($this->referenceToId($reference));

        if (isset($data['name'])) {
            $command->setName($data['name']);
        }
        if (isset($data['color'])) {
            $command->setColor($data['color']);
        }
        if (isset($data['share_customer'])) {
            $command->setShareCustomer(PrimitiveUtils::castStringBooleanIntoBoolean($data['share_customer']));
        }
        if (isset($data['share_stock'])) {
            $command->setShareStock(PrimitiveUtils::castStringBooleanIntoBoolean($data['share_stock']));
        }
        if (isset($data['share_order'])) {
            $command->setShareOrder(PrimitiveUtils::castStringBooleanIntoBoolean($data['share_order']));
        }
        if (isset($data['active'])) {
            $command->setActive(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']));
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (ShopException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete shop group :reference
     */
    public function deleteShopGroup(string $reference): void
    {
        try {
            $this->getCommandBus()->handle(new DeleteShopGroupCommand($this->referenceToId($reference)));
        } catch (ShopException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then shop group :reference should have the following properties:
     */
    public function assertShopGroupProperties(string $reference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $shopGroup = $this->getShopGroup($reference);

        if (isset($data['name'])) {
            Assert::assertSame($data['name'], $shopGroup->getName());
        }
        if (isset($data['color'])) {
            Assert::assertSame($data['color'], $shopGroup->getColor());
        }
        if (isset($data['share_customer'])) {
            Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($data['share_customer']), $shopGroup->isShareCustomer());
        }
        if (isset($data['share_stock'])) {
            Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($data['share_stock']), $shopGroup->isShareStock());
        }
        if (isset($data['share_order'])) {
            Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($data['share_order']), $shopGroup->isShareOrder());
        }
        if (isset($data['active'])) {
            Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']), $shopGroup->isActive());
        }
        if (isset($data['sharing_options_locked'])) {
            Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($data['sharing_options_locked']), $shopGroup->areSharingOptionsLocked());
        }
    }

    /**
     * @Then shop group :reference should not exist
     */
    public function assertShopGroupDoesNotExist(string $reference): void
    {
        try {
            $this->getShopGroup($reference);
        } catch (ShopGroupNotFoundException) {
            return;
        }

        throw new RuntimeException(sprintf('Shop group "%s" still exists', $reference));
    }

    /**
     * @Then I should get error that shop group was not found
     */
    public function assertLastErrorIsShopGroupNotFound(): void
    {
        $this->assertLastErrorIs(ShopGroupNotFoundException::class);
    }

    /**
     * @Then I should get error that shop group name is invalid
     */
    public function assertLastErrorIsInvalidName(): void
    {
        $this->assertLastErrorIs(ShopGroupConstraintException::class, ShopGroupConstraintException::INVALID_NAME);
    }

    /**
     * @Then I should get error that shop group color is invalid
     */
    public function assertLastErrorIsInvalidColor(): void
    {
        $this->assertLastErrorIs(ShopGroupConstraintException::class, ShopGroupConstraintException::INVALID_COLOR);
    }

    /**
     * @Then I should get error that shop group cannot share orders without sharing customers and stock
     */
    public function assertLastErrorIsShareOrderWithoutSharedCustomersAndStock(): void
    {
        $this->assertLastErrorIs(
            ShopGroupConstraintException::class,
            ShopGroupConstraintException::SHARE_ORDER_REQUIRES_SHARED_CUSTOMERS_AND_STOCK
        );
    }

    /**
     * @Then I should get error that shop group sharing options are locked
     */
    public function assertLastErrorIsSharingOptionsLocked(): void
    {
        $this->assertLastErrorIs(CannotUpdateShopGroupException::class, CannotUpdateShopGroupException::SHARING_OPTIONS_LOCKED);
    }

    /**
     * @Then I should get error that shop group with shops cannot be disabled
     */
    public function assertLastErrorIsCannotDisableGroupWithShops(): void
    {
        $this->assertLastErrorIs(CannotUpdateShopGroupException::class, CannotUpdateShopGroupException::CANNOT_DISABLE_GROUP_WITH_SHOPS);
    }

    /**
     * @Then I should get error that shop group with shops cannot be deleted
     */
    public function assertLastErrorIsCannotDeleteGroupWithShops(): void
    {
        $this->assertLastErrorIs(CannotDeleteShopGroupException::class, CannotDeleteShopGroupException::GROUP_HAS_SHOPS);
    }

    private function getShopGroup(string $reference): EditableShopGroup
    {
        /** @var EditableShopGroup $shopGroup */
        $shopGroup = $this->getQueryBus()->handle(new GetShopGroupForEditing($this->referenceToId($reference)));

        return $shopGroup;
    }

    /**
     * @When I am an employee with access to shops :shopReferences only
     */
    public function restrictEmployeeToShops(string $shopReferences): void
    {
        $shopIds = array_map(
            fn (string $shopReference): int => (int) $this->getSharedStorage()->get($shopReference),
            $this->splitList($shopReferences)
        );

        $this->getEmployeeContextDecorator()->setOverriddenEmployee(new Employee(
            id: 0,
            profileId: self::NON_SUPER_ADMIN_PROFILE_ID,
            languageId: 1,
            firstName: 'Restricted',
            lastName: 'Employee',
            email: 'restricted@prestashop.com',
            password: '',
            imageUrl: '',
            defaultTabId: 0,
            defaultShopId: $shopIds[0],
            associatedShopIds: $shopIds,
            associatedShopGroupIds: [],
        ));
    }

    /**
     * @Then the shop tree should list the following groups and shops:
     */
    public function assertShopTreeContains(TableNode $table): void
    {
        $shopTree = $this->getShopNamesByGroupName();

        foreach ($table->getRowsHash() as $groupName => $shopNames) {
            Assert::assertArrayHasKey($groupName, $shopTree, sprintf('Group "%s" is missing from the shop tree', $groupName));
            Assert::assertSame($this->splitList($shopNames), $shopTree[$groupName]);
        }
    }

    /**
     * @Then the shop tree should not list the groups :groupNames
     */
    public function assertShopTreeDoesNotContain(string $groupNames): void
    {
        $shopTree = $this->getShopNamesByGroupName();

        foreach ($this->splitList($groupNames) as $groupName) {
            Assert::assertArrayNotHasKey($groupName, $shopTree, sprintf('Group "%s" should not be in the shop tree', $groupName));
        }
    }

    /**
     * @return array<string, string[]>
     */
    private function getShopNamesByGroupName(): array
    {
        $shopNamesByGroupName = [];
        foreach ($this->getQueryBus()->handle(new GetShopTree()) as $group) {
            $shopNamesByGroupName[$group['name']] = array_values(array_column($group['shops'], 'name'));
        }

        return $shopNamesByGroupName;
    }

    /**
     * @return string[]
     */
    private function splitList(string $list): array
    {
        return '' === trim($list) ? [] : array_map('trim', explode(',', $list));
    }

    private function getEmployeeContextDecorator(): EmployeeContextDecorator
    {
        /** @var EmployeeContextDecorator $employeeContext */
        $employeeContext = CommonFeatureContext::getContainer()->get(EmployeeContextDecorator::class);

        return $employeeContext;
    }
}
