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

use OrderMessage;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\AddOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject\OrderMessageId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderMessageContext extends AbstractDomainFeatureContext
{
    /**
     * @When I specify :propertyName :propertyValue in default language for order message :reference
     */
    public function specifyPropertyInDefaultLanguage(string $propertyName, string $propertyValue, string $reference)
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
    public function addWithSpecifiedProperties(string $reference)
    {
        $key = sprintf('order_message_%s_props', $reference);

        $properties = $this->getSharedStorage()->get($key);

        /** @var OrderMessageId $orderMessageId */
        $orderMessageId = $this->getCommandBus()->handle(
            new AddOrderMessageCommand(
                $properties['name'],
                $properties['message']
            )
        );

        $this->getSharedStorage()->set($reference, new OrderMessage($orderMessageId->getValue()));
    }

    /**
     * @Then order :orderReference must have no customer message
     *
     * @param string $orderReference
     *
     * @throws RuntimeException
     */
    public function orderMustHaveNoCustomerMessage(string $orderReference)
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
    public function orderMustHaveCustomerMessage(string $orderReference, string $messageContent)
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
}
