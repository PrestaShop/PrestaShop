<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\BulkDeleteCustomerGroupsCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\DeleteCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\EditCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\UpdateDefaultCustomerGroupsCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\BulkCustomerGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\CannotDeleteGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetDefaultCustomerGroups;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\DefaultCustomerGroups;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Throwable;

class CustomerGroupFeatureContext extends AbstractDomainFeatureContext
{
    private const NON_EXISTING_GROUP_ID = 999999;

    /**
     * @When I create a customer group :customerGroupReference with the following details:
     *
     * @param string $customerGroupReference
     * @param TableNode $tableNode
     *
     * @throws Exception
     */
    public function createCustomerGroupUsingCommand(string $customerGroupReference, TableNode $tableNode)
    {
        $data = $this->localizeByRows($tableNode);

        try {
            $command = new AddCustomerGroupCommand(
                $data['name'],
                new DecimalNumber($data['reduction']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['displayPriceTaxExcluded']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['showPrice']),
                $this->referencesToIds($data['shopIds'])
            );

            /** @var GroupId $id */
            $id = $this->getCommandBus()->handle($command);
            $this->getSharedStorage()->set($customerGroupReference, $id->getValue());
        } catch (GroupConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I update customer group :customerGroupReference with the following details:
     *
     * @param string $customerGroupReference
     * @param TableNode $tableNode
     *
     * @throws Exception
     */
    public function updateCustomerGroupUsingCommand(string $customerGroupReference, TableNode $tableNode)
    {
        $data = $this->localizeByRows($tableNode);

        try {
            $command = new EditCustomerGroupCommand($this->referenceToId($customerGroupReference));
            if (!empty($data['name'])) {
                $command->setLocalizedNames($data['name']);
            }
            if (!empty($data['reduction'])) {
                $command->setReductionPercent(new DecimalNumber($data['reduction']));
            }
            if (!empty($data['displayPriceTaxExcluded'])) {
                $command->setDisplayPriceTaxExcluded(PrimitiveUtils::castStringBooleanIntoBoolean($data['displayPriceTaxExcluded']));
            }
            if (!empty($data['showPrice'])) {
                $command->setShowPrice(PrimitiveUtils::castStringBooleanIntoBoolean($data['showPrice']));
            }
            if (!empty($data['shopIds'])) {
                $command->setShopIds($this->referencesToIds($data['shopIds']));
            }

            $this->getCommandBus()->handle($command);
        } catch (GroupConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get an error that the customer group reduction is invalid
     */
    public function assertLastErrorIsInvalidReduction(): void
    {
        $this->assertLastErrorIs(GroupConstraintException::class, GroupConstraintException::INVALID_REDUCTION);
    }

    /**
     * @When I delete customer group :customerGroupReference
     *
     * @param string $customerGroupReference
     */
    public function deleteCustomerGroupUsingCommand(string $customerGroupReference)
    {
        try {
            $this->getCommandBus()->handle(new DeleteCustomerGroupCommand($this->referenceToId($customerGroupReference)));
        } catch (GroupException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I bulk delete customer groups :customerGroupReferences
     */
    public function bulkDeleteCustomerGroups(string $customerGroupReferences): void
    {
        try {
            $this->getCommandBus()->handle(new BulkDeleteCustomerGroupsCommand($this->referencesToIds($customerGroupReferences)));
        } catch (GroupException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I bulk delete an empty list of customer groups
     */
    public function bulkDeleteEmptyListOfCustomerGroups(): void
    {
        try {
            $this->getCommandBus()->handle(new BulkDeleteCustomerGroupsCommand([]));
        } catch (GroupException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Given customer group :customerGroupReference does not exist yet
     */
    public function setNonExistingCustomerGroupReference(string $customerGroupReference): void
    {
        $this->getSharedStorage()->set($customerGroupReference, self::NON_EXISTING_GROUP_ID);
    }

    /**
     * @Given the default customer groups for all shops are referenced as :visitorsReference, :guestsReference and :customersReference
     */
    public function referenceDefaultCustomerGroups(string $visitorsReference, string $guestsReference, string $customersReference): void
    {
        $defaultGroups = $this->getQueryBus()->handle(new GetDefaultCustomerGroups(ShopConstraint::allShops()));

        $this->getSharedStorage()->set($visitorsReference, $defaultGroups->getVisitorsGroupId());
        $this->getSharedStorage()->set($guestsReference, $defaultGroups->getGuestsGroupId());
        $this->getSharedStorage()->set($customersReference, $defaultGroups->getCustomersGroupId());
    }

    /**
     * @When /^I set the default customer groups (for all shops|for shop "[^"]+") to "([^"]+)", "([^"]+)" and "([^"]+)"$/
     */
    public function updateDefaultCustomerGroups(
        string $shopScope,
        string $visitorsReference,
        string $guestsReference,
        string $customersReference
    ): void {
        try {
            $this->getCommandBus()->handle(new UpdateDefaultCustomerGroupsCommand(
                $this->referenceToId($visitorsReference),
                $this->referenceToId($guestsReference),
                $this->referenceToId($customersReference),
                $this->getShopConstraintFromScope($shopScope)
            ));
        } catch (GroupException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then /^the default customer groups (for all shops|for shop "[^"]+") should be "([^"]+)", "([^"]+)" and "([^"]+)"$/
     */
    public function assertDefaultCustomerGroups(
        string $shopScope,
        string $visitorsReference,
        string $guestsReference,
        string $customersReference
    ): void {
        $defaultGroups = $this->getQueryBus()->handle(new GetDefaultCustomerGroups($this->getShopConstraintFromScope($shopScope)));

        Assert::assertEquals(
            new DefaultCustomerGroups(
                $this->referenceToId($visitorsReference),
                $this->referenceToId($guestsReference),
                $this->referenceToId($customersReference)
            ),
            $defaultGroups
        );
    }

    /**
     * @Then I should get an error that a default customer group cannot be deleted
     */
    public function assertDefaultCustomerGroupCannotBeDeleted(): void
    {
        $this->assertLastErrorIs(CannotDeleteGroupException::class, CannotDeleteGroupException::GROUP_USED_AS_DEFAULT);
    }

    /**
     * @Then I should get a bulk customer group exception refusing :expectedErrorsCount default group(s)
     */
    public function assertBulkExceptionRefusingDefaultGroups(int $expectedErrorsCount): void
    {
        /** @var BulkCustomerGroupException $lastError */
        $lastError = $this->assertLastErrorIs(BulkCustomerGroupException::class, BulkCustomerGroupException::FAILED_BULK_DELETE);
        $errorsSummary = implode(' | ', array_map(fn (Throwable $e) => $e::class . ': ' . $e->getMessage(), $lastError->getExceptions()));

        Assert::assertCount($expectedErrorsCount, $lastError->getExceptions(), $errorsSummary);
        foreach ($lastError->getExceptions() as $exception) {
            Assert::assertInstanceOf(CannotDeleteGroupException::class, $exception, $errorsSummary);
            Assert::assertSame(CannotDeleteGroupException::GROUP_USED_AS_DEFAULT, $exception->getCode(), $errorsSummary);
        }
    }

    /**
     * @Then I should get an error that the customer group id is invalid
     */
    public function assertLastErrorIsInvalidId(): void
    {
        $this->assertLastErrorIs(GroupConstraintException::class, GroupConstraintException::INVALID_ID);
    }

    /**
     * @Then I should get an error that the customer group is not associated to the shop
     */
    public function assertLastErrorIsGroupNotAssociatedToShop(): void
    {
        $this->assertLastErrorIs(GroupConstraintException::class, GroupConstraintException::GROUP_NOT_ASSOCIATED_TO_SHOP);
    }

    /**
     * @Then I should get an error that the customer group was not found
     */
    public function assertLastErrorIsGroupNotFound(): void
    {
        $this->assertLastErrorIs(GroupNotFoundException::class);
    }

    private function getShopConstraintFromScope(string $shopScope): ShopConstraint
    {
        if ('for all shops' === $shopScope) {
            return ShopConstraint::allShops();
        }

        preg_match('/^for shop "([^"]+)"$/', $shopScope, $matches);

        return ShopConstraint::shop($this->referenceToId($matches[1]));
    }

    /**
     * @Then customer group :customerGroupReference have the following values:
     */
    public function assertQueryCustomerGroupProperties($customerGroupReference, EditableCustomerGroup $expectedGroup)
    {
        Assert::assertEquals($expectedGroup, $this->getCustomerGroupForEditing($customerGroupReference));
    }

    /**
     * @Given customer group :customerGroupReference exists
     */
    public function assertCustomerGroupExists(string $customerGroupReference): void
    {
        $caughtException = null;
        try {
            $this->getCustomerGroupForEditing($customerGroupReference);
        } catch (GroupNotFoundException $e) {
            $caughtException = $e;
        }
        Assert::assertNull($caughtException, sprintf('Customer group %s was not found', $customerGroupReference));
    }

    /**
     * @Then customer group :customerGroupReference does not exist
     */
    public function assertCustomerGroupDoesNotExist(string $customerGroupReference): void
    {
        $caughtException = null;
        try {
            $this->getCustomerGroupForEditing($customerGroupReference);
        } catch (GroupNotFoundException $e) {
            $caughtException = $e;
        }
        Assert::assertNotNull($caughtException, sprintf('Customer group %s should not exist', $customerGroupReference));
    }

    /**
     * @Transform table:customer group,value
     *
     * @param TableNode $tableNode
     *
     * @return EditableCustomerGroup
     */
    public function transformEditableCustomerGroup(TableNode $tableNode): EditableCustomerGroup
    {
        $data = $this->localizeByRows($tableNode);

        return new EditableCustomerGroup(
            $this->referenceToId($data['reference_id']),
            $data['name'],
            new DecimalNumber($data['reduction']),
            PrimitiveUtils::castStringBooleanIntoBoolean($data['displayPriceTaxExcluded']),
            PrimitiveUtils::castStringBooleanIntoBoolean($data['showPrice']),
            $this->referencesToIds($data['shopIds'])
        );
    }

    /**
     * @param string $customerGroupReference
     *
     * @return EditableCustomerGroup
     */
    private function getCustomerGroupForEditing(string $customerGroupReference): EditableCustomerGroup
    {
        return $this->getQueryBus()->handle(new GetCustomerGroupForEditing($this->getSharedStorage()->get($customerGroupReference)));
    }
}
