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
use Country;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\State\Command\AddStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkDeleteStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkToggleStateStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\DeleteStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\EditStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\ToggleStateStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotAddStateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotUpdateStateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\Query\GetStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\State\QueryResult\EditableState;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use RuntimeException;
use State;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Zone;

class StateFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var int default shop id from configs
     */
    private $defaultLangId;

    public function __construct()
    {
        $configuration = CommonFeatureContext::getContainer()->get('prestashop.adapter.legacy.configuration');
        $this->defaultLangId = $configuration->get('PS_LANG_DEFAULT');
    }

    /**
     * @When I add new state :stateReference with following properties:
     *
     * @param string $stateReference
     * @param TableNode $table
     */
    public function createState(string $stateReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        try {
            /** @var StateId $stateId */
            $stateId = $this->getCommandBus()->handle(new AddStateCommand(
                (int) Country::getIdByName($this->defaultLangId, $data['country']),
                Zone::getIdByName($data['zone']),
                $data['name'],
                $data['iso_code'],
                PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled'])
            ));

            SharedStorage::getStorage()->set($stateReference, $stateId->getValue());
        } catch (CannotAddStateException|StateConstraintException|StateException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit state :stateReference with following properties:
     *
     * @param string $stateReference
     * @param TableNode $table
     */
    public function editState(string $stateReference, TableNode $table): void
    {
        $state = new State((int) SharedStorage::getStorage()->get($stateReference));

        $command = new EditStateCommand($state->id);

        $data = $table->getRowsHash();
        if (isset($data['name'])) {
            $command->setName((string) $data['name']);
        }
        if (isset($data['iso_code'])) {
            $command->setIsoCode((string) $data['iso_code']);
        }
        if (isset($data['enabled'])) {
            $command->setActive(PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']));
        }
        if (isset($data['country'])) {
            $command->setCountryId((int) Country::getIdByName($this->defaultLangId, $data['country']));
        }
        if (isset($data['zone'])) {
            $command->setZoneId((int) Zone::getIdByName($data['zone']));
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (CannotUpdateStateException|StateConstraintException|StateException|StateNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete state :stateReference
     *
     * @param string $stateReference
     */
    public function deleteState(string $stateReference): void
    {
        $state = new State((int) SharedStorage::getStorage()->get($stateReference));

        try {
            $this->getCommandBus()->handle(new DeleteStateCommand((int) $state->id));
        } catch (StateNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete states :stateReferences using bulk action
     *
     * @param string $stateReferences
     */
    public function bulkDeleteStates(string $stateReferences): void
    {
        $stateIds = [];
        foreach (PrimitiveUtils::castStringArrayIntoArray($stateReferences) as $stateReference) {
            $stateIds[] = (int) SharedStorage::getStorage()->get($stateReference);
        }

        try {
            $this->getCommandBus()->handle(new BulkDeleteStateCommand($stateIds));
        } catch (StateNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I toggle status of state :stateReference
     *
     * @param string $stateReference
     */
    public function toggleStatus(string $stateReference): void
    {
        $stateId = (int) SharedStorage::getStorage()->get($stateReference);

        $this->getCommandBus()->handle(new ToggleStateStatusCommand($stateId));
        SharedStorage::getStorage()->set($stateReference, $stateId);
    }

    /**
     * @When /^I (enable|disable) multiple states "(.+)" using bulk action$/
     *
     * @param string $action
     * @param string $stateReferences
     */
    public function bulkToggleStatus(string $action, string $stateReferences): void
    {
        $expectedStatus = 'enable' === $action;
        $stateIds = [];

        foreach (PrimitiveUtils::castStringArrayIntoArray($stateReferences) as $stateReference) {
            $stateIds[$stateReference] = (int) SharedStorage::getStorage()->get($stateReference);
        }

        $this->getCommandBus()->handle(new BulkToggleStateStatusCommand($expectedStatus, $stateIds));

        foreach ($stateIds as $reference => $id) {
            SharedStorage::getStorage()->set($reference, $id);
        }
    }

    /**
     * @Then state :reference name should be :name
     *
     * @param string $stateReference
     * @param string $name
     */
    public function assertStateName(string $stateReference, string $name): void
    {
        $state = new State((int) SharedStorage::getStorage()->get($stateReference));

        if ($state->name !== $name) {
            throw new RuntimeException(sprintf('State "%s" has "%s" name, but "%s" was expected.', $stateReference, $state->name, $name));
        }
    }

    /**
     * @Then state :reference country should be :name
     *
     * @param string $stateReference
     * @param string $name
     */
    public function assertStateCountry(string $stateReference, string $name): void
    {
        $state = new State((int) SharedStorage::getStorage()->get($stateReference));
        $country = new Country($state->id_country);

        if ($country->name[$this->defaultLangId] !== $name) {
            throw new RuntimeException(sprintf(
                'Country "%s" has "%s" name, but "%s" was expected.',
                $stateReference,
                $country->name,
                $name
            ));
        }
    }

    /**
     * @Then state :reference zone should be :name
     *
     * @param string $stateReference
     * @param string $name
     */
    public function assertStateZone(string $stateReference, string $name): void
    {
        $state = new State((int) SharedStorage::getStorage()->get($stateReference));
        $zone = new Zone($state->id_zone);

        if ($zone->name !== $name) {
            throw new RuntimeException(sprintf(
                'Zone "%s" has "%s" name, but "%s" was expected.',
                $stateReference,
                $zone->name,
                $name
            ));
        }
    }

    /**
     * @Given /^state "(.*)" is (enabled|disabled)?$/
     * @Then /^state "(.*)" should be (enabled|disabled)?$/
     *
     * @param string $stateReference
     * @param string $expectedStatus
     */
    public function assertStatus(string $stateReference, string $expectedStatus): void
    {
        $state = new State((int) SharedStorage::getStorage()->get($stateReference));

        $isEnabled = 'enabled' === $expectedStatus;
        $actualStatus = (bool) $state->active;

        if ($actualStatus !== $isEnabled) {
            throw new RuntimeException(sprintf('State "%s" is %s, but it was expected to be %s', $stateReference, $actualStatus ? 'enabled' : 'disabled', $expectedStatus));
        }
    }

    /**
     * @Then /^states "(.+)" should be (enabled|disabled)$/
     *
     * @param string $stateReferences
     * @param string $expectedStatus
     */
    public function assertMultipleStatesStatus(string $stateReferences, string $expectedStatus): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($stateReferences) as $stateReference) {
            $this->assertStatus($stateReference, $expectedStatus);
        }
    }

    /**
     * @Then /^state "(.+)" should be deleted$/
     *
     * @param string $stateReference
     */
    public function assertStateIsDeleted(string $stateReference): void
    {
        $query = new GetStateForEditing((int) SharedStorage::getStorage()->get($stateReference));
        try {
            $this->getQueryBus()->handle($query);

            throw new NoExceptionAlthoughExpectedException(sprintf('State %s exists, but it was expected to be deleted', $stateReference));
        } catch (StateNotFoundException $e) {
            SharedStorage::getStorage()->clear($stateReference);
        }
    }

    /**
     * @Then /^state "(.+)" should not be deleted$/
     *
     * @param string $stateReference
     */
    public function assertStateIsNotDeleted(string $stateReference): void
    {
        $query = new GetStateForEditing((int) SharedStorage::getStorage()->get($stateReference));

        $state = $this->getQueryBus()->handle($query);
        Assert::assertInstanceOf(EditableState::class, $state);
    }

    /**
     * @Then states :stateReferences should be deleted
     *
     * @param string $stateReferences
     */
    public function assertStatesAreDeleted(string $stateReferences): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($stateReferences) as $stateReference) {
            $this->assertStateIsDeleted($stateReference);
        }
    }

    /**
     * @Then I should get an error that the state has not been found
     */
    public function assertLastErrorStateNotFound(): void
    {
        $this->assertLastErrorIs(StateNotFoundException::class);
    }
}
