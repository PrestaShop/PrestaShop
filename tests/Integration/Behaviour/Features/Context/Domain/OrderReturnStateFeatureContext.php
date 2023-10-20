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
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\AddOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\BulkDeleteOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\DeleteOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\EditOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\BulkDeleteOrderReturnStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\DeleteOrderReturnStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Query\GetOrderReturnStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\QueryResult\EditableOrderReturnState;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderReturnStateFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var int default language id from configs
     */
    private $defaultLangId;

    public function __construct()
    {
        $configuration = CommonFeatureContext::getContainer()->get('prestashop.adapter.legacy.configuration');
        $this->defaultLangId = $configuration->get('PS_LANG_DEFAULT');
    }

    /**
     * @Given I add a new order return state :orderReturnStateReference with the following details:
     *
     * @param string $orderReturnStateReference
     * @param TableNode $table
     */
    public function addNewOrderReturnState(string $orderReturnStateReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        try {
            /** @var OrderReturnStateId $orderReturnStateId */
            $orderReturnStateId = $this->getCommandBus()->handle(new AddOrderReturnStateCommand(
                [
                    $this->defaultLangId => $data['name'],
                ],
                $data['color']
            ));

            SharedStorage::getStorage()->set($orderReturnStateReference, $orderReturnStateId->getValue());
        } catch (OrderReturnStateException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Given I update the order return state :orderReturnStateReference with the following details:
     *
     * @param string $orderReturnStateReference
     * @param TableNode $table
     */
    public function updateOrderReturnState(string $orderReturnStateReference, TableNode $table): void
    {
        $orderReturnStateId = SharedStorage::getStorage()->get($orderReturnStateReference);

        $editableOrderReturnState = new EditOrderReturnStateCommand((int) $orderReturnStateId);

        $data = $table->getRowsHash();
        if (isset($data['name'])) {
            $editableOrderReturnState->setName([
                $this->defaultLangId => $data['name'],
            ]);
        }
        if (isset($data['color'])) {
            $editableOrderReturnState->setColor($data['color']);
        }

        try {
            $this->getCommandBus()->handle($editableOrderReturnState);
        } catch (OrderReturnStateException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete the order return state :orderReturnStateReference
     *
     * @param string $orderReturnStateReference
     */
    public function deleteOrderReturnState(string $orderReturnStateReference): void
    {
        $orderReturnStateId = SharedStorage::getStorage()->get($orderReturnStateReference);

        try {
            $this->getCommandBus()->handle(new DeleteOrderReturnStateCommand($orderReturnStateId));
        } catch (DeleteOrderReturnStateException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I bulk delete order return states :orderReturnStateReferences
     *
     * @param string $orderReturnStateReferences
     */
    public function bulkDeleteOrderReturnState(string $orderReturnStateReferences): void
    {
        $orderReturnStateReferences = explode(',', $orderReturnStateReferences);
        $orderReturnStatesId = [];
        foreach ($orderReturnStateReferences as $orderReturnStateReference) {
            $orderReturnStatesId[] = SharedStorage::getStorage()->get($orderReturnStateReference);
        }

        try {
            $this->getCommandBus()->handle(new BulkDeleteOrderReturnStateCommand($orderReturnStatesId));
        } catch (BulkDeleteOrderReturnStateException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then the order return state :orderReturnStateReference should have the following details:
     *
     * @param string $orderReturnStateReference
     * @param TableNode $table
     */
    public function checkOrderReturnStateDetails(string $orderReturnStateReference, TableNode $table): void
    {
        $editableOrderReturnState = $this->getOrderReturnState($orderReturnStateReference);

        $this->assertLastErrorIsNull();

        $data = $table->getRowsHash();

        $localizedNames = $editableOrderReturnState->getLocalizedNames();
        Assert::assertIsArray($localizedNames);
        Assert::assertArrayHasKey($this->defaultLangId, $localizedNames);
        Assert::assertEquals($data['name'], $localizedNames[$this->defaultLangId]);
        Assert::assertEquals($data['color'], $editableOrderReturnState->getColor());
    }

    /**
     * @Then the order return state :orderReturnStateReference should exist
     *
     * @param string $orderReturnStateReference
     */
    public function checkOrderReturnStateExists(string $orderReturnStateReference): void
    {
        $this->getOrderReturnState($orderReturnStateReference);

        $this->assertLastErrorIsNull();
    }

    /**
     * @Then the order return state :orderReturnStateReference shouldn't exist
     *
     * @param string $orderReturnStateReference
     */
    public function checkOrderReturnStateNotExists(string $orderReturnStateReference): void
    {
        $this->getOrderReturnState($orderReturnStateReference);

        $this->assertLastErrorIs(OrderReturnStateNotFoundException::class);
    }

    /**
     * @param string $orderReturnStateReference
     *
     * @return EditableOrderReturnState|null
     */
    private function getOrderReturnState(string $orderReturnStateReference): ?EditableOrderReturnState
    {
        try {
            return $this->getQueryBus()->handle(
                new GetOrderReturnStateForEditing(SharedStorage::getStorage()->get($orderReturnStateReference))
            );
        } catch (OrderReturnStateNotFoundException $e) {
            $this->setLastException($e);
        }

        return null;
    }
}
