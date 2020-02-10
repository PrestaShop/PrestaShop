<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\ZoneByIdChoiceProvider;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\Command\AddStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Query\GetStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\State\QueryResult\EditableState;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CountryByIdChoiceProvider;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class LocationFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add new state with following details:
     *
     * @param TableNode $table
     */
    public function addNewStateWithFollowingDetails(TableNode $table)
    {
        $testCaseData = $table->getRowsHash();
        /** @var CountryByIdChoiceProvider $countryByIdChoiceProvider */
        $countryByIdChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.country_by_id');
        $availableCountries = $countryByIdChoiceProvider->getChoices();
        $countryId = $availableCountries[$testCaseData['Country']];
        /** @var ZoneByIdChoiceProvider $zoneByIdChoiceProvider */
        $zoneByIdChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.zone_by_id');
        $zones = $zoneByIdChoiceProvider->getChoices([]);
        $zoneId = $zones[$testCaseData['Zone']];
        /** @var StateId $stateId */
        $stateId = $this->getCommandBus()->handle(new AddStateCommand(
            $countryId,
            $zoneId,
            $testCaseData['Name'],
            $testCaseData['ISO code'],
            $testCaseData['Status']
        ));
        SharedStorage::getStorage()->set($testCaseData['Name'], $stateId->getValue());
    }

    /**
     * @Then there is state with following details:
     *
     * @param TableNode $table
     */
    public function thereIsStateWithFollowingDetails(TableNode $table)
    {
        $testCaseData = $table->getRowsHash();
        $expectedEditableState = $this->mapToEditableState($testCaseData);
        /** @var EditableState $editableState */
        $editableState = $this->getQueryBus()->handle(
            new GetStateForEditing($expectedEditableState->getStateId()->getValue())
        );
        Assert::assertEquals($expectedEditableState, $editableState);
    }

    /**
     * @When I add new state with invalid following details:
     *
     * @param TableNode $table
     */
    public function addNewStateWithInvalidFollowingDetails(TableNode $table)
    {
        $testCaseData = $table->getRowsHash();
        /** @var CountryByIdChoiceProvider $countryByIdChoiceProvider */
        $countryByIdChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.country_by_id');
        $availableCountries = $countryByIdChoiceProvider->getChoices();
        $countryId = $availableCountries[$testCaseData['Country']];
        /** @var ZoneByIdChoiceProvider $zoneByIdChoiceProvider */
        $zoneByIdChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.zone_by_id');
        $zones = $zoneByIdChoiceProvider->getChoices([]);
        $zoneId = $zones[$testCaseData['Zone']];
        try {
            /** @var StateId $stateId */
            $stateId = $this->getCommandBus()->handle(new AddStateCommand(
                $countryId,
                $zoneId,
                $testCaseData['Name'],
                $testCaseData['ISO code'],
                $testCaseData['Status']
            ));
            SharedStorage::getStorage()->set($testCaseData['Name'], $stateId->getValue());
        } catch (StateConstraintException $e) {
            return;
        }
        throw new \RuntimeException('Expected StateConstraintException was not thrown');
    }

    /**
     * @Then there is no state with name :stateName
     *
     * @param string $stateName
     */
    public function thereIsNoStateWithName(string $stateName)
    {
        try {
            SharedStorage::getStorage()->get($stateName);
        } catch (\RuntimeException $e) {
            return;
        }
        throw new \RuntimeException('Expected exception was not thrown');
    }

    /**
     * @param array $testCaseData
     *
     * @return EditableState
     */
    public function mapToEditableState(array $testCaseData): EditableState
    {
        /** @var CountryByIdChoiceProvider $countryByIdChoiceProvider */
        $countryByIdChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.country_by_id');
        $availableCountries = $countryByIdChoiceProvider->getChoices();
        $countryId = $availableCountries[$testCaseData['Country']];
        /** @var ZoneByIdChoiceProvider $zoneByIdChoiceProvider */
        $zoneByIdChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.zone_by_id');
        $zones = $zoneByIdChoiceProvider->getChoices([]);
        $zoneId = $zones[$testCaseData['Zone']];

        return new EditableState(
            new StateId(SharedStorage::getStorage()->get($testCaseData['Name'])),
            new CountryId($countryId),
            new ZoneId($zoneId),
            $testCaseData['Name'],
            $testCaseData['ISO code'],
            $testCaseData['Status']
        );
    }
}
