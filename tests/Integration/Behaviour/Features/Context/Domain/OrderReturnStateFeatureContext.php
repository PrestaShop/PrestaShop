<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
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
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderReturnStateFeatureContext extends AbstractDomainFeatureContext
{
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
                    $this->getDefaultLangId() => $data['name'],
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
                $this->getDefaultLangId() => $data['name'],
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
        Assert::assertArrayHasKey($this->getDefaultLangId(), $localizedNames);
        Assert::assertEquals($data['name'], $localizedNames[$this->getDefaultLangId()]);
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
