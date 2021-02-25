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

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\AddZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\BulkDeleteZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\BulkToggleZoneStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\DeleteZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\EditZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\ToggleZoneStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Query\GetZoneForEditing;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Zone;

class ZoneFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var int default shop id from configs
     */
    private $defaultShopId;

    public function __construct()
    {
        $configuration = CommonFeatureContext::getContainer()->get('prestashop.adapter.legacy.configuration');
        $this->defaultShopId = $configuration->get('PS_SHOP_DEFAULT');
    }

    /**
     * @When I add new zone :zoneReference with following properties:
     *
     * @param string $zoneReference
     * @param TableNode $table
     */
    public function createZone(string $zoneReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        try {
            $zoneId = $this->getCommandBus()->handle(new AddZoneCommand(
                $data['name'],
                PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']),
                [$this->defaultShopId]
            ));
            $this->getSharedStorage()->set($zoneReference, new Zone($zoneId->getValue()));
        } catch (ZoneException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit zone :zoneReference with following properties:
     *
     * @param string $zoneReference
     * @param TableNode $table
     */
    public function editZone(string $zoneReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        /** @var Zone $zone */
        $zone = SharedStorage::getStorage()->get($zoneReference);

        $zoneId = (int) $zone->id;
        $command = new EditZoneCommand($zoneId);

        if (isset($data['name'])) {
            $command->setName($data['name']);
        }

        if (isset($data['enabled'])) {
            $command->setEnabled(PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']));
        }

        $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($zoneReference, new Zone($zoneId));
    }

    /**
     * @When I delete zone :zoneReference
     *
     * @param string $zoneReference
     */
    public function deleteZone(string $zoneReference): void
    {
        /** @var Zone $zone */
        $zone = SharedStorage::getStorage()->get($zoneReference);

        $this->getCommandBus()->handle(new DeleteZoneCommand((int) $zone->id));
    }

    /**
     * @When I delete zones: :zoneReferences using bulk action
     *
     * @param string $zoneReferences
     */
    public function bulkDeleteZones(string $zoneReferences): void
    {
        $zoneIds = [];
        foreach (PrimitiveUtils::castStringArrayIntoArray($zoneReferences) as $zoneReference) {
            $zoneIds[] = (int) SharedStorage::getStorage()->get($zoneReference)->id;
        }

        $this->getCommandBus()->handle(new BulkDeleteZoneCommand($zoneIds));
    }

    /**
     * @When I toggle status of zone :zoneReference
     *
     * @param string $zoneReference
     */
    public function toggleStatus(string $zoneReference): void
    {
        /** @var Zone $zone */
        $zone = SharedStorage::getStorage()->get($zoneReference);
        $zoneId = (int) $zone->id;

        $this->getCommandBus()->handle(new ToggleZoneStatusCommand($zoneId));
        SharedStorage::getStorage()->set($zoneReference, new Zone($zoneId));
    }

    /**
     * @When /^I (enable|disable) multiple zones: "(.+)" using bulk action$/
     *
     * @param string $action
     * @param string $zoneReferences
     */
    public function bulkToggleStatus(string $action, string $zoneReferences): void
    {
        $expectedStatus = 'enable' === $action;
        $zoneIds = [];

        foreach (PrimitiveUtils::castStringArrayIntoArray($zoneReferences) as $zoneReference) {
            $zone = SharedStorage::getStorage()->get($zoneReference);
            $zoneIds[$zoneReference] = (int) $zone->id;
        }

        $this->getCommandBus()->handle(new BulkToggleZoneStatusCommand($expectedStatus, $zoneIds));

        foreach ($zoneIds as $reference => $id) {
            SharedStorage::getStorage()->set($reference, new Zone($id));
        }
    }

    /**
     * @Then zone :reference name should be :name
     *
     * @param string $zoneReference
     * @param string $name
     */
    public function assertZoneName(string $zoneReference, string $name): void
    {
        $zone = SharedStorage::getStorage()->get($zoneReference);

        if ($zone->name !== $name) {
            throw new RuntimeException(sprintf('Zone "%s" has "%s" name, but "%s" was expected.', $zoneReference, $zone->name, $name));
        }
    }

    /**
     * @Given /^zone "(.*)" is (enabled|disabled)?$/
     * @Then /^zone "(.*)" should be (enabled|disabled)?$/
     *
     * @param string $zoneReference
     * @param string $expectedStatus
     */
    public function assertStatus(string $zoneReference, string $expectedStatus): void
    {
        /** @var Zone $zone */
        $zone = SharedStorage::getStorage()->get($zoneReference);

        $isEnabled = 'enabled' === $expectedStatus;
        $actualStatus = (bool) $zone->active;

        if ($actualStatus !== $isEnabled) {
            throw new RuntimeException(sprintf('Zone "%s" is %s, but it was expected to be %s', $zoneReference, $actualStatus ? 'enabled' : 'disabled', $expectedStatus));
        }
    }

    /**
     * @Then /^zones: "(.+)" should be (enabled|disabled)$/
     *
     * @param string $zoneReferences
     * @param string $expectedStatus
     */
    public function assertMultipleZonesStatus(string $zoneReferences, string $expectedStatus): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($zoneReferences) as $zoneReference) {
            $this->assertStatus($zoneReference, $expectedStatus);
        }
    }

    /**
     * @Then zone :zoneReference should be deleted
     *
     * @param string $zoneReference
     */
    public function assertZoneIsDeleted(string $zoneReference): void
    {
        /** @var Zone $zone */
        $zone = SharedStorage::getStorage()->get($zoneReference);

        try {
            $query = new GetZoneForEditing((int) $zone->id);
            $this->getQueryBus()->handle($query);

            throw new NoExceptionAlthoughExpectedException(sprintf('Zone %s exists, but it was expected to be deleted', $zoneReference));
        } catch (ZoneNotFoundException $e) {
            SharedStorage::getStorage()->clear($zoneReference);
        }
    }

    /**
     * @Then zones: :zoneReferences should be deleted
     *
     * @param string $zoneReferences
     */
    public function assertZonesAreDeleted(string $zoneReferences): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($zoneReferences) as $zoneReference) {
            $this->assertZoneIsDeleted($zoneReference);
        }
    }
}
