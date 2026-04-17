<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use OrderMessage;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\AddOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\EditOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception\OrderMessageNameAlreadyUsedException;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject\OrderMessageId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderMessageContext extends AbstractDomainFeatureContext
{
    /**
     * @When I specify :propertyName :propertyValue in default language for order message :reference
     */
    public function specifyPropertyInDefaultLanguage(string $propertyName, string $propertyValue, string $reference): void
    {
        $key = sprintf('order_message_%s_props', $reference);

        $defaultLangId = $this->getContainer()->get('prestashop.adapter.legacy.configuration')->get('PS_LANG_DEFAULT');

        $properties = $this->getSharedStorage()->getWithDefault($key, []);
        $properties[$propertyName][$defaultLangId] = $propertyValue;

        $this->getSharedStorage()->set($key, $properties);
    }

    /**
     * @When I add order message :reference with specified properties
     */
    public function addWithSpecifiedProperties(string $reference): void
    {
        $key = sprintf('order_message_%s_props', $reference);

        $properties = $this->getSharedStorage()->get($key);

        /* @var OrderMessageId $orderMessageId */
        try {
            $orderMessageId = $this->getCommandBus()->handle(
                new AddOrderMessageCommand(
                    $properties['name'],
                    $properties['message']
                )
            );

            $this->getSharedStorage()->set($reference, new OrderMessage($orderMessageId->getValue()));
        } catch (OrderMessageNameAlreadyUsedException $exception) {
            $this->setLastException($exception);
        }
    }

    /**
     * @When I edit order message :reference with specified properties
     */
    public function editWithSpecifiedProperties(string $reference): void
    {
        $key = sprintf('order_message_%s_props', $reference);

        $properties = $this->getSharedStorage()->get($key);

        /** @var OrderMessage $orderMessage */
        $orderMessage = $this->getSharedStorage()->get($reference);

        try {
            $this->getCommandBus()->handle(
                new EditOrderMessageCommand(
                    (int) $orderMessage->id,
                    $properties['name'],
                    $properties['message'] ?? null
                )
            );
        } catch (OrderMessageNameAlreadyUsedException $exception) {
            $this->setLastException($exception);
        }
    }

    /**
     * @Then order :orderReference must have no customer message
     *
     * @param string $orderReference
     *
     * @throws RuntimeException
     */
    public function orderMustHaveNoCustomerMessage(string $orderReference): void
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        if (0 !== count($orderForViewing->getMessages()->getMessages())) {
            throw new RuntimeException(sprintf('Order #%s do have messages', $orderId));
        }
    }

    /**
     * @Then order :orderReference must have customer message with content :messageContent
     *
     * @param string $orderReference
     * @param string $messageContent
     *
     * @throws RuntimeException
     */
    public function orderMustHaveCustomerMessage(string $orderReference, string $messageContent): void
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        $messageFound = false;
        foreach ($orderForViewing->getMessages()->getMessages() as $orderMessageForViewing) {
            if (0 === strcmp($orderMessageForViewing->getMessage(), $messageContent)) {
                $messageFound = true;
            }
        }
        if (!$messageFound) {
            throw new RuntimeException(sprintf('Message "%s" not found in Order #%s messages', $messageContent, $orderId));
        }
    }

    /**
     * @Then I should get error that an order message with this name already exists
     */
    public function assertLastErrorIsOrderMessageNameAlreadyUsed(): void
    {
        $this->assertLastErrorIs(OrderMessageNameAlreadyUsedException::class);
    }
}
