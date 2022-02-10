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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Country;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkDeleteStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkToggleStateStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\DeleteStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\ToggleStateStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\Query\GetStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\State\QueryResult\EditableState;
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
     * @When I delete state :stateReference
     *
     * @param string $stateReference
     */
    public function deleteState(string $stateReference): void
    {
        /** @var State $state */
        $state = SharedStorage::getStorage()->get($stateReference);

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
            $stateIds[] = (int) SharedStorage::getStorage()->get($stateReference)->id;
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
        /** @var State $state */
        $state = SharedStorage::getStorage()->get($stateReference);
        $stateId = (int) $state->id;

        $this->getCommandBus()->handle(new ToggleStateStatusCommand($stateId));
        SharedStorage::getStorage()->set($stateReference, new State($stateId));
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
            $state = SharedStorage::getStorage()->get($stateReference);
            $stateIds[$stateReference] = (int) $state->id;
        }

        $this->getCommandBus()->handle(new BulkToggleStateStatusCommand($expectedStatus, $stateIds));

        foreach ($stateIds as $reference => $id) {
            SharedStorage::getStorage()->set($reference, new State($id));
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
        $state = SharedStorage::getStorage()->get($stateReference);

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
        /** @var State $state */
        $state = SharedStorage::getStorage()->get($stateReference);

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
        /** @var State $state */
        $state = SharedStorage::getStorage()->get($stateReference);

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
        /** @var State $state */
        $state = SharedStorage::getStorage()->get($stateReference);

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
        /** @var State $state */
        $state = SharedStorage::getStorage()->get($stateReference);
        $query = new GetStateForEditing((int) $state->id);
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
        /** @var State $state */
        $state = SharedStorage::getStorage()->get($stateReference);
        $query = new GetStateForEditing((int) $state->id);

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
