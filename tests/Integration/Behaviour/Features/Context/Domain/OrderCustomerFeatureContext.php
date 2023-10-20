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

use Customer;
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderCustomerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderCustomerFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Then /^the customer of the order "(.+)" has the APE Code "(.*)"$/
     *
     * @param string $orderReference
     * @param string $ape
     */
    public function orderCustomerHasAPECode(string $orderReference, string $ape): void
    {
        $orderCustomerForViewing = $this->getCustomer($orderReference);

        Assert::assertSame(
            $ape,
            $orderCustomerForViewing->getApe(),
            sprintf(
                'Expected customer with id "%d" has APE code "%s" but received "%s"',
                $orderCustomerForViewing->getId(),
                $orderCustomerForViewing->getApe(),
                $ape
            )
        );
    }

    /**
     * @Then /^the customer of the order "(.+)" has the SIRET Code "(.*)"$/
     *
     * @param string $orderReference
     * @param string $siret
     */
    public function orderCustomerHasSIRETCode(string $orderReference, string $siret): void
    {
        $orderCustomerForViewing = $this->getCustomer($orderReference);

        Assert::assertSame(
            $siret,
            $orderCustomerForViewing->getSiret(),
            sprintf(
                'Expected customer with id "%d" has SIRET code "%s" but received "%s"',
                $orderCustomerForViewing->getId(),
                $orderCustomerForViewing->getSiret(),
                $siret
            )
        );
    }

    /**
     * @Then /^the customer of the order "(.+)" has been deleted$/
     *
     * @param string $orderReference
     */
    public function orderCustomerIsDeleted(string $orderReference): void
    {
        $orderCustomerForViewing = $this->getCustomer($orderReference);

        $customer = new Customer($orderCustomerForViewing->getId());

        Assert::assertTrue(
            (bool) $customer->delete(),
            sprintf(
                'Expected customer with id "%d" has been deleted',
                $orderCustomerForViewing->getId()
            )
        );
    }

    /**
     * @Then /^the customer lastname of the order "(.*)" is "(.+)"$/
     *
     * @param string $orderReference
     */
    public function orderCustomerCheckLastName(string $orderReference, string $value): void
    {
        $orderCustomerForViewing = $this->getCustomer($orderReference);

        Assert::assertSame(
            $value,
            $orderCustomerForViewing->getLastName(),
            sprintf(
                'Expected customer lastname to be "%s"',
                $value
            )
        );
    }

    /**
     * @Then /^the customer firstname of the order "(.*)" is "(.+)"$/
     *
     * @param string $orderReference
     */
    public function orderCustomerCheckFirstName(string $orderReference, string $value): void
    {
        $orderCustomerForViewing = $this->getCustomer($orderReference);

        Assert::assertSame(
            $value,
            $orderCustomerForViewing->getFirstName(),
            sprintf(
                'Expected customer firstname to be "%s"',
                $value
            )
        );
    }

    /**
     * @Then /^the customer id of the order "(.*)" is "(.+)"$/
     *
     * @param string $orderReference
     */
    public function orderCustomerPropertyCheck(string $orderReference, string $value): void
    {
        $orderCustomerForViewing = $this->getCustomer($orderReference);

        Assert::assertSame(
            (int) $value,
            $orderCustomerForViewing->getId(),
            sprintf(
                'Expected customer id to be "%d"',
                $value
            )
        );
    }

    protected function getCustomer(string $orderReference): OrderCustomerForViewing
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        return $orderForViewing->getCustomer();
    }
}
