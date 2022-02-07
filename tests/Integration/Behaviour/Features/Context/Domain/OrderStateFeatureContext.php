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

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\AddOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\BulkDeleteOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\DeleteOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\EditOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\BulkDeleteOrderStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\DeleteOrderStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Query\GetOrderStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderState\QueryResult\EditableOrderState;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderStateFeatureContext extends AbstractDomainFeatureContext
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
     * @Given I add a new order state with the following details:
     *
     * @param TableNode $table
     */
    public function addNewOrderState(TableNode $table): void
    {
        $data = $table->getRowsHash();

        try {
            /** @var OrderStateId $orderStateId */
            $orderStateId = $this->getCommandBus()->handle(new AddOrderStateCommand(
                [
                    $this->defaultLangId => $data['name'],
                ],
                $data['color'],
                (bool) $data['isLoggable'],
                (bool) $data['isInvoice'],
                (bool) $data['isHidden'],
                (bool) $data['hasSendMail'],
                (bool) $data['hasPdfInvoice'],
                (bool) $data['hasPdfDelivery'],
                (bool) $data['isShipped'],
                (bool) $data['isPaid'],
                (bool) $data['isDelivery'],
                []
            ));

            SharedStorage::getStorage()->set($data['name'], $orderStateId->getValue());
        } catch (OrderStateException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Given I update the order state with name :name with the following details:
     *
     * @param string $name
     * @param TableNode $table
     */
    public function updateOrderState(string $name, TableNode $table): void
    {
        $orderStateId = SharedStorage::getStorage()->get($name);

        $editableOrderState = new EditOrderStateCommand((int) $orderStateId);

        $data = $table->getRowsHash();
        if (isset($data['name'])) {
            $editableOrderState->setName([
                $this->defaultLangId => $data['name'],
            ]);
        }
        if (isset($data['color'])) {
            $editableOrderState->setColor($data['color']);
        }
        if (isset($data['isLoggable'])) {
            $editableOrderState->setLoggable((bool) $data['isLoggable']);
        }
        if (isset($data['isInvoice'])) {
            $editableOrderState->setInvoice((bool) $data['isInvoice']);
        }
        if (isset($data['isHidden'])) {
            $editableOrderState->setHidden((bool) $data['isHidden']);
        }
        if (isset($data['hasSendMail'])) {
            $editableOrderState->setSendEmail((bool) $data['hasSendMail']);
        }
        if (isset($data['hasPdfInvoice'])) {
            $editableOrderState->setPdfInvoice((bool) $data['hasPdfInvoice']);
        }
        if (isset($data['isShipped'])) {
            $editableOrderState->setShipped((bool) $data['isShipped']);
        }
        if (isset($data['isPaid'])) {
            $editableOrderState->setPaid((bool) $data['isPaid']);
        }
        if (isset($data['isDelivery'])) {
            $editableOrderState->setDelivery((bool) $data['isDelivery']);
        }

        try {
            $this->getCommandBus()->handle($editableOrderState);
        } catch (OrderStateException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete the order state with name :name
     *
     * @param string $name
     */
    public function deleteOrderState(string $name): void
    {
        $orderStateId = SharedStorage::getStorage()->get($name);

        try {
            $this->getCommandBus()->handle(new DeleteOrderStateCommand($orderStateId));
        } catch (DeleteOrderStateException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I bulk delete order states with name :names
     *
     * @param string $names
     */
    public function bulkDeleteOrderState(string $names): void
    {
        $names = explode(',', $names);
        $orderStatesId = [];
        foreach ($names as $name) {
            $orderStatesId[] = SharedStorage::getStorage()->get($name);
        }

        try {
            $this->getCommandBus()->handle(new BulkDeleteOrderStateCommand($orderStatesId));
        } catch (BulkDeleteOrderStateException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then the order state with name :name should have the following details:
     *
     * @param string $name
     * @param TableNode $table
     */
    public function checkOrderStateDetails(string $name, TableNode $table): void
    {
        $editableOrderState = $this->getOrderState($name);

        $this->assertLastErrorIsNull();

        $data = $table->getRowsHash();

        $localizedNames = $editableOrderState->getLocalizedNames();
        Assert::assertIsArray($localizedNames);
        Assert::assertArrayHasKey($this->defaultLangId, $localizedNames);
        Assert::assertEquals($data['name'], $localizedNames[$this->defaultLangId]);
        Assert::assertEquals($data['color'], $editableOrderState->getColor());
    }

    /**
     * @Then the order state with name :name should exist
     *
     * @param string $name
     */
    public function checkOrderStateExists(string $name): void
    {
        $this->getOrderState($name);

        $this->assertLastErrorIsNull();
    }

    /**
     * @Then the order state with name :name shouldn't exist
     *
     * @param string $name
     */
    public function checkOrderStateNotExists(string $name): void
    {
        $this->getOrderState($name);

        $this->assertLastErrorIs(OrderStateNotFoundException::class);
    }

    /**
     * @param string $isoCode
     *
     * @return EditableOrderState|null
     */
    private function getOrderState(string $isoCode): ?EditableOrderState
    {
        try {
            return $this->getQueryBus()->handle(
                new GetOrderStateForEditing(SharedStorage::getStorage()->get($isoCode))
            );
        } catch (OrderStateNotFoundException $e) {
            $this->setLastException($e);
        }

        return null;
    }
}
