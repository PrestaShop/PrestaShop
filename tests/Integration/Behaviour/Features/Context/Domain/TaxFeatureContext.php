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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Country;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\AddTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkDeleteTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkToggleTaxStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\DeleteTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\EditTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\ToggleTaxStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Query\GetTaxForEditing;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use RuntimeException;
use State;
use Tax;
use TaxCalculator;
use TaxRule;
use TaxRulesGroup;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Tests\Resources\Resetter\TaxesResetter;

class TaxFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var int default language id from configuration
     */
    private $defaultLangId;

    public function __construct()
    {
        $this->defaultLangId = CommonFeatureContext::getContainer()
            ->get('prestashop.adapter.legacy.configuration')
            ->get('PS_LANG_DEFAULT');
    }

    /**
     * @BeforeFeature @restore-taxes-before-feature
     */
    public static function restoreTaxesTablesBeforeFeature(): void
    {
        TaxesResetter::resetTaxes();
    }

    /**
     * @AfterFeature @restore-taxes-after-feature
     */
    public static function restoreTaxesTablesAfterFeature(): void
    {
        TaxesResetter::resetTaxes();
    }

    /**
     * @When I add new tax :taxReference with following properties:
     */
    public function createTax(string $taxReference, TableNode $table): void
    {
        $this->createTaxUsingCommand($taxReference, $table->getRowsHash());
    }

    /**
     * @When I edit tax :taxReference with following properties:
     */
    public function editTaxUsingCommand($taxReference, TableNode $table)
    {
        $data = $table->getRowsHash();

        /** @var Tax $tax */
        $tax = SharedStorage::getStorage()->get($taxReference);
        $taxId = (int) $tax->id;
        $command = new EditTaxCommand($taxId);
        if (isset($data['name'])) {
            $command->setLocalizedNames([$this->defaultLangId => $data['name']]);
        }
        if (isset($data['rate'])) {
            $command->setRate($data['rate']);
        }

        if (isset($data['is_enabled'])) {
            $command->setEnabled(PrimitiveUtils::castStringBooleanIntoBoolean($data['is_enabled']));
        }
        $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($taxReference, new Tax($taxId));
    }

    /**
     * @When /^I (enable|disable)? tax "(.*)"$/
     */
    public function toggleStatus($action, $taxReference)
    {
        $expectedStatus = 'enable' === $action;

        /** @var Tax $tax */
        $tax = SharedStorage::getStorage()->get($taxReference);
        $taxId = (int) $tax->id;

        $this->getCommandBus()->handle(new ToggleTaxStatusCommand($taxId, $expectedStatus));
        SharedStorage::getStorage()->set($taxReference, new Tax($taxId));
    }

    /**
     * @When /^I (enable|disable)? taxes: "([^"]*)"$/
     */
    public function bulkToggleStatus($action, $taxReferences)
    {
        $taxReferences = PrimitiveUtils::castStringArrayIntoArray($taxReferences);
        $expectedStatus = 'enable' === $action;

        $idsByReference = [];
        foreach ($taxReferences as $reference) {
            $tax = SharedStorage::getStorage()->get($reference);
            $idsByReference[$reference] = (int) $tax->id;
        }

        $this->getCommandBus()->handle(new BulkToggleTaxStatusCommand(
            $idsByReference,
            $expectedStatus
        ));

        foreach ($idsByReference as $reference => $id) {
            SharedStorage::getStorage()->set($reference, new Tax($id));
        }
    }

    /**
     * @When I delete tax :taxReference
     */
    public function deleteTax($taxReference)
    {
        $tax = SharedStorage::getStorage()->get($taxReference);
        $taxId = (int) $tax->id;

        $this->getCommandBus()->handle(new DeleteTaxCommand($taxId));
    }

    /**
     * @When I delete taxes: :taxReferences in bulk action
     */
    public function bulkDeleteTax($taxReferences)
    {
        $taxIds = [];
        foreach (PrimitiveUtils::castStringArrayIntoArray($taxReferences) as $taxReference) {
            $tax = SharedStorage::getStorage()->get($taxReference);
            $taxIds[] = (int) $tax->id;
        }

        $this->getCommandBus()->handle(new BulkDeleteTaxCommand($taxIds));
    }

    /**
     * @Then taxes: :taxReferences should be deleted
     */
    public function assertTaxesAreDeleted($taxReferences)
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($taxReferences) as $taxReference) {
            $this->assertTaxIsDeleted($taxReference);
        }
    }

    /**
     * @Then tax :taxReference should be deleted
     */
    public function assertTaxIsDeleted($taxReference)
    {
        $tax = SharedStorage::getStorage()->get($taxReference);
        $taxId = (int) $tax->id;
        try {
            $this->getQueryBus()->handle(new GetTaxForEditing($taxId));

            throw new NoExceptionAlthoughExpectedException(sprintf('Tax %s expected to be deleted, but it was found', $taxReference));
        } catch (TaxNotFoundException $e) {
            SharedStorage::getStorage()->clear($taxReference);
        }
    }

    /**
     * @Then tax :taxReference name in default language should be :name
     */
    public function assertTaxNameInDefaultLang($taxReference, $name)
    {
        /** @var Tax $tax */
        $tax = SharedStorage::getStorage()->get($taxReference);

        if ($tax->name[$this->defaultLangId] !== $name) {
            throw new RuntimeException(sprintf('Tax "%s" has "%s" name, but "%s" was expected.', $taxReference, $tax->name, $name));
        }
    }

    /**
     * @Then tax :taxReference rate should be :rate
     */
    public function assertTaxRate($taxReference, $rate)
    {
        /** @var Tax $tax */
        $tax = SharedStorage::getStorage()->get($taxReference);

        if ($tax->rate !== $rate) {
            throw new RuntimeException(sprintf('Tax "%s" has "%s" rate, but "%s" was expected.', $taxReference, $tax->rate, $rate));
        }
    }

    /**
     * @Then /^taxes: "(.*)" should be (enabled|disabled)?$/
     */
    public function assertTaxesStatus($taxReferences, $status)
    {
        $taxReferences = PrimitiveUtils::castStringArrayIntoArray($taxReferences);

        foreach ($taxReferences as $reference) {
            $this->assertTaxStatus($reference, $status);
        }
    }

    /**
     * @Then /^tax "(.*)" should be (enabled|disabled)?$/
     * @Given /^tax "(.*)" is (enabled|disabled)?$/
     */
    public function assertTaxStatus($taxReference, $status)
    {
        /** @var Tax $tax */
        $tax = SharedStorage::getStorage()->get($taxReference);
        $isEnabled = $status === 'enabled';
        $actualStatus = (bool) $tax->active;

        if ($isEnabled !== $actualStatus) {
            throw new RuntimeException(sprintf('Tax "%s" is %s, but it was expected to be %s', $taxReference, $actualStatus ? 'enabled' : 'disabled', $status));
        }
    }

    /**
     * @param string $taxReference
     * @param array $data
     */
    private function createTaxUsingCommand(string $taxReference, array $data): void
    {
        $command = new AddTaxCommand(
            [$this->defaultLangId => $data['name']],
            $data['rate'],
            PrimitiveUtils::castStringBooleanIntoBoolean($data['is_enabled'])
        );

        /** @var TaxId $taxId */
        $taxId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($taxReference, new Tax($taxId->getValue()));
    }

    /**
     * @Then I add the tax rule group :taxGroupReference for the tax :taxReference with the following conditions:
     */
    public function addTaxRuleGroupToTax(string $taxGroupReference, string $taxReference, TableNode $table)
    {
        $data = $table->getRowsHash();

        $taxRulesGroup = new TaxRulesGroup();
        $taxRulesGroup->name = $data['name'];
        $taxRulesGroup->active = true;
        $taxRulesGroup->deleted = false;
        $taxRulesGroup->save();
        SharedStorage::getStorage()->set($taxGroupReference, $taxRulesGroup->id);

        $tax = SharedStorage::getStorage()->get($taxReference);
        $taxRule = new TaxRule();
        $taxRule->id_tax = $tax->id;
        $taxRule->id_tax_rules_group = $taxRulesGroup->id;
        $taxRule->behavior = TaxCalculator::ONE_TAX_ONLY_METHOD;
        $taxRule->id_country = Country::getByIso($data['country']);
        $taxRule->id_state = isset($data['state']) ? State::getIdByIso($data['state']) : 0;
        $taxRule->save();
    }

    /**
     * @Then I add the tax rule :taxReference for tax rule group :taxGroupReference:
     */
    public function addTaxRuleToTaxRulesGroup(string $taxGroupReference, string $taxReference, TableNode $table)
    {
        $data = $table->getRowsHash();
        $taxGroupId = SharedStorage::getStorage()->get($taxGroupReference);

        $tax = SharedStorage::getStorage()->get($taxReference);
        $taxRule = new TaxRule();
        $taxRule->id_tax = $tax->id;
        $taxRule->id_tax_rules_group = $taxGroupId;
        $taxRule->behavior = TaxCalculator::ONE_TAX_ONLY_METHOD;
        $taxRule->id_country = Country::getByIso($data['country']);
        $taxRule->id_state = isset($data['state']) ? State::getIdByIso($data['state']) : 0;
        $taxRule->save();
    }

    /**
     * @Then I delete tax rules that has tax :taxReference:
     */
    public function deleteTaxRuleFromTaxRulesGroup(string $taxReference)
    {
        $tax = SharedStorage::getStorage()->get($taxReference);
        TaxRule::deleteTaxRuleByIdTax($tax->id);
    }
}
