<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\AddTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\BulkDeleteTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\BulkSetTaxRulesGroupStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\DeleteTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\EditTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\SetTaxRulesGroupStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Query\GetTaxRulesGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryResult\EditableTaxRulesGroup;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use RuntimeException;
use TaxRulesGroup;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class TaxRulesGroupFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @param string $name
     *
     * @return TaxRulesGroup
     */
    public static function getTaxRulesGroupByName(string $name): TaxRulesGroup
    {
        $taxRulesGroupId = (int) TaxRulesGroup::getIdByName($name);
        $taxRulesGroup = new TaxRulesGroup($taxRulesGroupId);

        if ($taxRulesGroupId !== (int) $taxRulesGroup->id) {
            throw new RuntimeException(sprintf('Tax rules group "%s" not found', $name));
        }

        return $taxRulesGroup;
    }

    /**
     * @Given tax rules group named :name exists
     *
     * @param string $name
     */
    public function assertTaxRuleGroupExists(string $name): void
    {
        self::getTaxRulesGroupByName($name);
    }

    /**
     * @Given I identify tax rules group named :name as :taxRuleGroupReference
     *
     * @param string $name
     */
    public function identifyTaxRulesGroup(string $name, string $taxRuleGroupReference): void
    {
        $taxRulesGroup = self::getTaxRulesGroupByName($name);
        $this->getSharedStorage()->set($taxRuleGroupReference, $taxRulesGroup->id);
    }

    /**
     * @Then I should get error that tax rules group does not exist
     */
    public function assertLastErrorIsTaxRulesGroupNotFound(): void
    {
        $this->assertLastErrorIs(TaxRulesGroupNotFoundException::class);
    }

    /**
     * @When I add a new tax rules group :taxRulesGroupReference with the following properties:
     */
    public function createTaxRulesGroup(string $taxRulesGroupReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        $command = new AddTaxRulesGroupCommand(
            $data['name'],
            PrimitiveUtils::castStringBooleanIntoBoolean($data['is_enabled'])
        );

        /** @var TaxRulesGroupId $taxRulesGroupId */
        $taxRulesGroupId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($taxRulesGroupReference, $taxRulesGroupId->getValue());
    }

    /**
     * @When I edit the tax rules group :taxRulesGroupReference with the following properties:
     */
    public function editTaxRulesGroup(string $taxRulesGroupReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        $taxRulesGroupId = SharedStorage::getStorage()->get($taxRulesGroupReference);
        $command = new EditTaxRulesGroupCommand($taxRulesGroupId);
        if (isset($data['name'])) {
            $command->setName($data['name']);
        }
        if (isset($data['is_enabled'])) {
            $command->setEnabled(PrimitiveUtils::castStringBooleanIntoBoolean($data['is_enabled']));
        }
        $this->getCommandBus()->handle($command);
    }

    /**
     * @When /^I (enable|disable)? tax rules group "(.*)"$/
     */
    public function toggleStatusTaxRulesGroup(bool $expectedStatus, string $taxRulesGroupReference): void
    {
        $taxRulesGroupId = SharedStorage::getStorage()->get($taxRulesGroupReference);

        $this->getCommandBus()->handle(new SetTaxRulesGroupStatusCommand($taxRulesGroupId, $expectedStatus));
    }

    /**
     * @When I delete tax rules group :taxRulesGroupReference
     */
    public function deleteTaxRulesGroup(string $taxRulesGroupReference): void
    {
        $taxRulesGroupId = SharedStorage::getStorage()->get($taxRulesGroupReference);

        $this->getCommandBus()->handle(new DeleteTaxRulesGroupCommand($taxRulesGroupId));
    }

    /**
     * @When /^I (enable|disable)? tax rules groups: "([^"]*)"$/
     */
    public function bulkToggleStatusTaxRulesGroup(bool $expectedStatus, string $taxRulesGroupReference): void
    {
        $taxRulesGroupReferences = PrimitiveUtils::castStringArrayIntoArray($taxRulesGroupReference);

        $idsByReference = [];
        foreach ($taxRulesGroupReferences as $reference) {
            $idsByReference[$reference] = SharedStorage::getStorage()->get($reference);
        }

        $this->getCommandBus()->handle(new BulkSetTaxRulesGroupStatusCommand(
            $idsByReference,
            $expectedStatus
        ));
    }

    /**
     * @When /^I bulk delete tax rules groups: "([^"]*)"$/
     */
    public function bulkDeleteTaxRulesGroup(string $taxRulesGroupReference): void
    {
        $taxRulesGroupReferences = PrimitiveUtils::castStringArrayIntoArray($taxRulesGroupReference);

        $idsByReference = [];
        foreach ($taxRulesGroupReferences as $reference) {
            $idsByReference[$reference] = SharedStorage::getStorage()->get($reference);
        }

        $this->getCommandBus()->handle(new BulkDeleteTaxRulesGroupCommand($idsByReference));
    }

    /**
     * @Then tax rules group :taxRulesGroupReference name should be :name
     */
    public function assertTaxRulesGroupName(string $taxRulesGroupReference, string $name): void
    {
        $editableTaxRulesGroup = $this->getEditableTaxRulesGroup($taxRulesGroupReference);

        if ($editableTaxRulesGroup->getName() !== $name) {
            throw new RuntimeException(sprintf(
                'Tax Rules Group "%s" has "%s" name, but "%s" was expected.',
                $taxRulesGroupReference,
                $editableTaxRulesGroup->getName(),
                $name
            ));
        }
    }

    /**
     * @Then /^tax rules group "(.*)" should be (enabled|disabled)$/
     * @Given /^tax rules group "(.*)" is (enabled|disabled)$/
     */
    public function assertTaxRulesGroupStatus(string $taxRulesGroupReference, bool $isEnabled): void
    {
        $editableTaxRulesGroup = $this->getEditableTaxRulesGroup($taxRulesGroupReference);

        if ($isEnabled !== $editableTaxRulesGroup->isActive()) {
            throw new RuntimeException(sprintf(
                'Tax Rules Group "%s" is %s, but it was expected to be %s',
                $taxRulesGroupReference,
                $editableTaxRulesGroup->isActive() ? 'enabled' : 'disabled',
                $isEnabled ? 'enabled' : 'disabled'
            ));
        }
    }

    /**
     * @Then /^tax rules groups: "(.*)" should be (enabled|disabled)$/
     */
    public function assertTaxRulesGroupsStatus(string $taxRulesGroupsReferences, bool $status): void
    {
        $taxRulesGroupsReferences = PrimitiveUtils::castStringArrayIntoArray($taxRulesGroupsReferences);

        foreach ($taxRulesGroupsReferences as $reference) {
            $this->assertTaxRulesGroupStatus($reference, $status);
        }
    }

    /**
     * @Then /^tax rules groups: "(.*)" should be deleted$/
     */
    public function assertTaxRulesGroupsExist(string $taxRulesGroupsReferences): void
    {
        $taxRulesGroupsReferences = PrimitiveUtils::castStringArrayIntoArray($taxRulesGroupsReferences);

        foreach ($taxRulesGroupsReferences as $reference) {
            $this->assertTaxRulesGroupExist($reference);
        }
    }

    /**
     * @Then tax rules group :taxReference should be deleted
     */
    public function assertTaxRulesGroupExist(string $taxRulesGroupReference): void
    {
        $taxRulesGroupId = SharedStorage::getStorage()->get($taxRulesGroupReference);

        try {
            $this->getQueryBus()->handle(new GetTaxRulesGroupForEditing($taxRulesGroupId));

            throw new NoExceptionAlthoughExpectedException(
                sprintf(
                    'Tax rules group  %s expected to be deleted, but it was found',
                    $taxRulesGroupReference
                )
            );
        } catch (TaxRulesGroupNotFoundException $e) {
            SharedStorage::getStorage()->clear($taxRulesGroupReference);
        }
    }

    private function getEditableTaxRulesGroup(string $taxRulesGroupReference): EditableTaxRulesGroup
    {
        $taxRulesGroupId = SharedStorage::getStorage()->get($taxRulesGroupReference);

        $query = new GetTaxRulesGroupForEditing($taxRulesGroupId);

        /** @var EditableTaxRulesGroup $editableTaxRulesGroup */
        $editableTaxRulesGroup = $this->getQueryBus()->handle($query);

        return $editableTaxRulesGroup;
    }
}
