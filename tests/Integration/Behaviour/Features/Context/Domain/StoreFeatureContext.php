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

use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\BulkDeleteStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\BulkUpdateStoreStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\DeleteStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\ToggleStoreStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Store\Query\GetStoreForEditing;
use RuntimeException;
use Store;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class StoreFeatureContext extends AbstractDomainFeatureContext
{
    private const DUMMY_STORE_ID = 1;

    /**
     * @When I toggle :reference
     *
     * @param string $reference
     */
    public function disableStoreWithReference(string $reference): void
    {
        $toggleStatusCommand = new ToggleStoreStatusCommand(self::DUMMY_STORE_ID);
        $this->getCommandBus()->handle($toggleStatusCommand);
    }

    /**
     * @When /^I (enable|disable) multiple stores "(.+)" using bulk action$/
     *
     * @param string $action
     * @param string $storeReferences
     */
    public function bulkToggleStatus(string $action, string $storeReferences): void
    {
        $expectedStatus = 'enable' === $action;
        $storeIds = [];

        foreach (PrimitiveUtils::castStringArrayIntoArray($storeReferences) as $storeReference) {
            $store = SharedStorage::getStorage()->get($storeReference);
            $storeIds[$storeReference] = (int) $store->id;
        }

        $bulkUpdateCommand = new BulkUpdateStoreStatusCommand($expectedStatus, $storeIds);
        $this->getCommandBus()->handle($bulkUpdateCommand);

        foreach ($storeIds as $reference => $id) {
            SharedStorage::getStorage()->set($reference, new Store($id));
        }
    }

    /**
     * @When I delete store :storeReference
     *
     * @param string $storeReference
     */
    public function deleteStore(string $storeReference): void
    {
        /** @var Store $store */
        $store = SharedStorage::getStorage()->get($storeReference);

        try {
            $this->getCommandBus()->handle(new DeleteStoreCommand((int) $store->id));
        } catch (StoreNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I delete stores "(.+)" using bulk action$/
     *
     * @param string $storeReferences
     */
    public function bulkDeleteStores(string $storeReferences): void
    {
        $storeIds = [];
        foreach (PrimitiveUtils::castStringArrayIntoArray($storeReferences) as $storeReference) {
            $storeIds[] = (int) SharedStorage::getStorage()->get($storeReference)->id;
        }

        try {
            $this->getCommandBus()->handle(new BulkDeleteStoreCommand($storeIds));
        } catch (StoreNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then /^the store "(.*)" should have status (enabled|disabled)$/
     *
     * @param string $reference
     * @param string $status
     */
    public function isStoreToggledWithReference(string $reference, string $status): void
    {
        $isEnabled = $status === 'enabled';
        $storeForEditingQuery = new GetStoreForEditing(self::DUMMY_STORE_ID);
        $storeUpdated = $this->getQueryBus()->handle($storeForEditingQuery);
        Assert::assertEquals((bool) $storeUpdated->isActive(), $isEnabled);
    }

    /**
     * @Then /^stores "(.+)" should be (enabled|disabled)$/
     *
     * @param string $storeReferences
     * @param string $expectedStatus
     */
    public function assertMultipleStoreStatus(string $storeReferences, string $expectedStatus): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($storeReferences) as $storeReference) {
            $this->assertStoreStatus($storeReference, $expectedStatus);
        }
    }

    public function assertStoreStatus(string $storeReference, string $expectedStatus): void
    {
        /** @var Store $store */
        $store = SharedStorage::getStorage()->get($storeReference);

        $isEnabled = 'enabled' === $expectedStatus;
        $actualStatus = (bool) $store->active;

        if ($actualStatus !== $isEnabled) {
            throw new RuntimeException(sprintf('Store "%s" is %s, but it was expected to be %s', $storeReference, $actualStatus ? 'enabled' : 'disabled', $expectedStatus));
        }
    }

    /**
     * @Then /^stores "(.+)" should (exist|be deleted)$/
     *
     * @param string $storeReferences
     * @param string $expectedPresence
     */
    public function assertMultipleStorePresence(string $storeReferences, string $expectedPresence): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($storeReferences) as $storeReference) {
            $this->assertStorePresence($storeReference, $expectedPresence);
        }
    }

    public function assertStorePresence(string $storeReference, string $expectedPresence): void
    {
        /** @var Store $store */
        $store = SharedStorage::getStorage()->get($storeReference);

        $isToBePresent = 'exist' === $expectedPresence;
        $isToBeDeleted = 'be deleted' === $expectedPresence;
        $query = new GetStoreForEditing((int) $store->id);
        try {
            $storeQueried = $this->getQueryBus()->handle($query);
            if ($storeQueried && $isToBeDeleted) {
                throw new RuntimeException(sprintf('Store "%s" is present, but it was expected to be deleted', $storeReference));
            }
        } catch (StoreNotFoundException $e) {
            if ($isToBePresent) {
                throw new RuntimeException(sprintf('Store "%s" is present, but it was expected to be deleted', $storeReference));
            }
            SharedStorage::getStorage()->clear($storeReference);
        }
    }
}
