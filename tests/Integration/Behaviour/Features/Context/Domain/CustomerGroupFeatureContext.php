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
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\BulkDeleteCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\DeleteCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\EditCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\ToggleCustomerGroupShowPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\CustomerGroupId;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CustomerGroupFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I create a customer group :customerGroupReference with the following details:
     *
     * @throws Exception
     */
    public function createCustomerGroupUsingCommand(string $customerGroupReference, TableNode $tableNode): void
    {
        $data = $this->localizeByRows($tableNode);

        $command = new AddCustomerGroupCommand(
            $data['name'],
            new DecimalNumber($data['reduction']),
            (bool) $data['displayPriceTaxExcluded'],
            (bool) $data['showPrice'],
            $this->referencesToIds($data['shopIds'])
        );

        /** @var CustomerGroupId $id */
        $id = $this->getCommandBus()->handle($command);
        $this->getSharedStorage()->set($customerGroupReference, $id->getValue());
    }

    /**
     * @When I update customer group :customerGroupReference with the following details:
     *
     * @throws Exception
     */
    public function updateCustomerGroupUsingCommand(string $customerGroupReference, TableNode $tableNode): void
    {
        $data = $this->localizeByRows($tableNode);

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
    }

    /**
     * @When I toggle show prices for customer group :customerGroupReference
     */
    public function toggleShowPricesForCustomerGroup(string $customerGroupReference): void
    {
        $this->getCommandBus()->handle(
            new ToggleCustomerGroupShowPricesCommand($this->referenceToId($customerGroupReference))
        );
    }

    /**
     * @When I delete customer group :customerGroupReference
     */
    public function deleteCustomerGroupUsingCommand(string $customerGroupReference): void
    {
        $this->getCommandBus()->handle(new DeleteCustomerGroupCommand($this->referenceToId($customerGroupReference)));
    }

    /**
     * @When I bulk delete customer groups :customerGroupReferences
     */
    public function bulkDeleteCustomerGroupsUsingCommand(string $customerGroupReferences): void
    {
        $this->getCommandBus()->handle(
            new BulkDeleteCustomerGroupCommand($this->referencesToIds($customerGroupReferences))
        );
    }

    /**
     * @When I set :reduction% category reduction for category :categoryReference on customer group :customerGroupReference
     */
    public function setCategoryReductionForGroup(string $reduction, string $categoryReference, string $customerGroupReference): void
    {
        $categoryId = $this->referenceToId($categoryReference);
        $command = new EditCustomerGroupCommand($this->referenceToId($customerGroupReference));
        $command->setCategoryReductions([$categoryId => (float) $reduction]);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I clear category reductions for customer group :customerGroupReference
     */
    public function clearCategoryReductionsForGroup(string $customerGroupReference): void
    {
        $command = new EditCustomerGroupCommand($this->referenceToId($customerGroupReference));
        $command->setCategoryReductions([]);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I set authorized module ids :moduleIds for customer group :customerGroupReference
     */
    public function setAuthorizedModuleIdsForGroup(string $moduleIds, string $customerGroupReference): void
    {
        $ids = $moduleIds === '' ? [] : array_map('intval', explode(',', $moduleIds));
        $command = new EditCustomerGroupCommand($this->referenceToId($customerGroupReference));
        $command->setAuthorizedModuleIds($ids);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @Then customer group :customerGroupReference have the following values:
     */
    public function assertQueryCustomerGroupProperties(string $customerGroupReference, EditableCustomerGroup $expectedGroup): void
    {
        Assert::assertEquals($expectedGroup, $this->getCustomerGroupForEditing($customerGroupReference));
    }

    /**
     * @Then category :categoryReference should have a :reduction% reduction for customer group :customerGroupReference
     */
    public function assertCategoryReductionForGroup(string $categoryReference, string $reduction, string $customerGroupReference): void
    {
        $categoryId = $this->referenceToId($categoryReference);
        $actualGroup = $this->getCustomerGroupForEditing($customerGroupReference);
        $categoryReductions = $actualGroup->getCategoryReductions();
        Assert::assertArrayHasKey($categoryId, $categoryReductions, sprintf('No reduction found for category "%s" (id %d)', $categoryReference, $categoryId));
        Assert::assertEquals(new DecimalNumber($reduction), $categoryReductions[$categoryId]['reduction']);
    }

    /**
     * @Then category :categoryReference should not have a reduction for customer group :customerGroupReference
     */
    public function assertNoCategoryReductionForGroup(string $categoryReference, string $customerGroupReference): void
    {
        $categoryId = $this->referenceToId($categoryReference);
        $actualGroup = $this->getCustomerGroupForEditing($customerGroupReference);
        Assert::assertArrayNotHasKey($categoryId, $actualGroup->getCategoryReductions(), sprintf('Unexpected reduction for category "%s" (id %d)', $categoryReference, $categoryId));
    }

    /**
     * @Then customer group :customerGroupReference has no authorized modules
     */
    public function assertNoAuthorizedModulesForGroup(string $customerGroupReference): void
    {
        $actualGroup = $this->getCustomerGroupForEditing($customerGroupReference);
        Assert::assertEmpty($actualGroup->getAuthorizedModuleIds(), sprintf('Expected no authorized modules for customer group "%s"', $customerGroupReference));
    }

    /**
     * @Given customer group :customerGroupReference exists
     */
    public function assertCustomerGroupExists(string $customerGroupReference): void
    {
        $customerGroup = $this->getCustomerGroupForEditing($customerGroupReference);
        Assert::assertNotNull($customerGroup, sprintf('Customer group %s was not found', $customerGroupReference));
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
     */
    public function transformEditableCustomerGroup(TableNode $tableNode): EditableCustomerGroup
    {
        $data = $this->localizeByRows($tableNode);
        // Fetch actual values so assertEqual passes for fields not specified in the table
        $actualGroup = $this->getCustomerGroupForEditing($data['reference_id']);

        return new EditableCustomerGroup(
            $this->referenceToId($data['reference_id']),
            $data['name'],
            new DecimalNumber($data['reduction']),
            PrimitiveUtils::castStringBooleanIntoBoolean($data['displayPriceTaxExcluded']),
            PrimitiveUtils::castStringBooleanIntoBoolean($data['showPrice']),
            $this->referencesToIds($data['shopIds']),
            $actualGroup->getCategoryReductions(),
            $actualGroup->getAuthorizedModuleIds(),
        );
    }

    private function getCustomerGroupForEditing(string $customerGroupReference): EditableCustomerGroup
    {
        return $this->getQueryBus()->handle(
            new GetCustomerGroupForEditing($this->getSharedStorage()->get($customerGroupReference))
        );
    }
}
