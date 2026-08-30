<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Db;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command\AddTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command\BulkDeleteTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command\DeleteTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command\EditTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\CommandResult\AddTaxRuleResult;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\TaxRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Query\GetTaxRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\QueryResult\EditableTaxRule;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class TaxRuleFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add a tax rule :taxRuleReference to group :groupReference with the following properties:
     */
    public function addTaxRule(string $taxRuleReference, string $groupReference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $groupId = SharedStorage::getStorage()->get($groupReference);

        $command = new AddTaxRuleCommand(
            $groupId,
            (int) $data['country'],
            (int) $data['tax']
        );

        if (isset($data['behavior'])) {
            $command->setBehavior((int) $data['behavior']);
        }
        if (isset($data['zipcode']) && $data['zipcode'] !== '') {
            $command->setZipCode($data['zipcode']);
        }
        if (isset($data['description']) && $data['description'] !== '') {
            $command->setDescription($data['description']);
        }

        /** @var AddTaxRuleResult $result */
        $result = $this->getCommandBus()->handle($command);

        // Update group reference in case historization changed the ID
        SharedStorage::getStorage()->set($groupReference, $result->getTaxRulesGroupId()->getValue());

        // Find the last created tax rule in this group to store its reference
        $taxRuleId = $this->findLastTaxRuleInGroup($result->getTaxRulesGroupId()->getValue());
        SharedStorage::getStorage()->set($taxRuleReference, $taxRuleId);
    }

    /**
     * @When I edit tax rule :taxRuleReference with the following properties:
     */
    public function editTaxRule(string $taxRuleReference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $taxRuleId = SharedStorage::getStorage()->get($taxRuleReference);

        $command = new EditTaxRuleCommand($taxRuleId);

        if (isset($data['country'])) {
            $command->setCountryId((int) $data['country']);
        }
        if (isset($data['state'])) {
            $command->setStateId((int) $data['state']);
        }
        if (isset($data['tax'])) {
            $command->setTaxId((int) $data['tax']);
        }
        if (isset($data['behavior'])) {
            $command->setBehavior((int) $data['behavior']);
        }
        if (isset($data['zipcode'])) {
            $command->setZipCode($data['zipcode']);
        }
        if (isset($data['description'])) {
            $command->setDescription($data['description']);
        }

        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I delete tax rule :taxRuleReference
     */
    public function deleteTaxRule(string $taxRuleReference): void
    {
        $taxRuleId = SharedStorage::getStorage()->get($taxRuleReference);
        $this->getCommandBus()->handle(new DeleteTaxRuleCommand($taxRuleId));
    }

    /**
     * @When I bulk delete tax rules :taxRuleReferences from group :groupReference
     */
    public function bulkDeleteTaxRules(string $taxRuleReferences, string $groupReference): void
    {
        $references = PrimitiveUtils::castStringArrayIntoArray($taxRuleReferences);
        $groupId = SharedStorage::getStorage()->get($groupReference);

        $ids = [];
        foreach ($references as $reference) {
            $ids[] = SharedStorage::getStorage()->get($reference);
        }

        $this->getCommandBus()->handle(new BulkDeleteTaxRuleCommand($groupId, $ids));
    }

    /**
     * @Then tax rule :taxRuleReference should exist in group :groupReference
     */
    public function assertTaxRuleExistsInGroup(string $taxRuleReference, string $groupReference): void
    {
        $editableTaxRule = $this->getEditableTaxRule($taxRuleReference);
        $expectedGroupId = SharedStorage::getStorage()->get($groupReference);

        if ($editableTaxRule->getTaxRulesGroupId()->getValue() !== $expectedGroupId) {
            throw new RuntimeException(sprintf(
                'Tax rule "%s" belongs to group %d, expected group %d',
                $taxRuleReference,
                $editableTaxRule->getTaxRulesGroupId()->getValue(),
                $expectedGroupId
            ));
        }
    }

    /**
     * @Then tax rule :taxRuleReference country should be :expectedCountryId
     */
    public function assertTaxRuleCountry(string $taxRuleReference, int $expectedCountryId): void
    {
        $editableTaxRule = $this->getEditableTaxRule($taxRuleReference);

        if ($editableTaxRule->getCountryId() !== $expectedCountryId) {
            throw new RuntimeException(sprintf(
                'Tax rule "%s" has country %d, expected %d',
                $taxRuleReference,
                $editableTaxRule->getCountryId(),
                $expectedCountryId
            ));
        }
    }

    /**
     * @Then tax rule :taxRuleReference tax should be :expectedTaxId
     */
    public function assertTaxRuleTax(string $taxRuleReference, int $expectedTaxId): void
    {
        $editableTaxRule = $this->getEditableTaxRule($taxRuleReference);

        if ($editableTaxRule->getTaxId() !== $expectedTaxId) {
            throw new RuntimeException(sprintf(
                'Tax rule "%s" has tax %d, expected %d',
                $taxRuleReference,
                $editableTaxRule->getTaxId(),
                $expectedTaxId
            ));
        }
    }

    /**
     * @Then tax rule :taxRuleReference behavior should be :expectedBehavior
     */
    public function assertTaxRuleBehavior(string $taxRuleReference, int $expectedBehavior): void
    {
        $editableTaxRule = $this->getEditableTaxRule($taxRuleReference);

        if ($editableTaxRule->getBehavior() !== $expectedBehavior) {
            throw new RuntimeException(sprintf(
                'Tax rule "%s" has behavior %d, expected %d',
                $taxRuleReference,
                $editableTaxRule->getBehavior(),
                $expectedBehavior
            ));
        }
    }

    /**
     * @Then tax rule :taxRuleReference description should be :expectedDescription
     */
    public function assertTaxRuleDescription(string $taxRuleReference, string $expectedDescription): void
    {
        $editableTaxRule = $this->getEditableTaxRule($taxRuleReference);

        if ($editableTaxRule->getDescription() !== $expectedDescription) {
            throw new RuntimeException(sprintf(
                'Tax rule "%s" has description "%s", expected "%s"',
                $taxRuleReference,
                $editableTaxRule->getDescription(),
                $expectedDescription
            ));
        }
    }

    /**
     * @Then tax rule :taxRuleReference zipcode from should be :expectedFrom
     */
    public function assertTaxRuleZipcodeFrom(string $taxRuleReference, string $expectedFrom): void
    {
        $editableTaxRule = $this->getEditableTaxRule($taxRuleReference);

        if ($editableTaxRule->getZipcodeFrom() !== $expectedFrom) {
            throw new RuntimeException(sprintf(
                'Tax rule "%s" has zipcode_from "%s", expected "%s"',
                $taxRuleReference,
                $editableTaxRule->getZipcodeFrom(),
                $expectedFrom
            ));
        }
    }

    /**
     * @Then tax rule :taxRuleReference zipcode to should be :expectedTo
     */
    public function assertTaxRuleZipcodeTo(string $taxRuleReference, string $expectedTo): void
    {
        $editableTaxRule = $this->getEditableTaxRule($taxRuleReference);

        if ($editableTaxRule->getZipcodeTo() !== $expectedTo) {
            throw new RuntimeException(sprintf(
                'Tax rule "%s" has zipcode_to "%s", expected "%s"',
                $taxRuleReference,
                $editableTaxRule->getZipcodeTo(),
                $expectedTo
            ));
        }
    }

    /**
     * @Then tax rule :taxRuleReference should not exist
     */
    public function assertTaxRuleNotExist(string $taxRuleReference): void
    {
        $taxRuleId = SharedStorage::getStorage()->get($taxRuleReference);

        try {
            $this->getQueryBus()->handle(new GetTaxRuleForEditing($taxRuleId));

            throw new NoExceptionAlthoughExpectedException(sprintf(
                'Tax rule "%s" was expected to be deleted, but it was found',
                $taxRuleReference
            ));
        } catch (TaxRuleNotFoundException) {
            SharedStorage::getStorage()->clear($taxRuleReference);
        }
    }

    private function getEditableTaxRule(string $taxRuleReference): EditableTaxRule
    {
        $taxRuleId = SharedStorage::getStorage()->get($taxRuleReference);

        /** @var EditableTaxRule $editableTaxRule */
        $editableTaxRule = $this->getQueryBus()->handle(new GetTaxRuleForEditing($taxRuleId));

        return $editableTaxRule;
    }

    /**
     * Finds the last (highest ID) tax rule in a group.
     */
    private function findLastTaxRuleInGroup(int $groupId): int
    {
        $result = Db::getInstance()->getValue(
            'SELECT MAX(id_tax_rule) FROM ' . _DB_PREFIX_ . 'tax_rule WHERE id_tax_rules_group = ' . (int) $groupId
        );

        return (int) $result;
    }
}
