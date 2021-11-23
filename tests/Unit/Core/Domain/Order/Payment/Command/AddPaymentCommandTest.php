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

namespace Tests\Unit\Core\Domain\Order\Payment\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\NegativePaymentAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Payment\Command\AddPaymentCommand;

class AddPaymentCommandTest extends TestCase
{
    public function testAmountIsNegative(): void
    {
        $this->expectException(NegativePaymentAmountException::class);
        $this->expectExceptionMessage('The amount should be greater than 0.');
        new AddPaymentCommand(1, date('Y-m-d'), 'Check', -1, 2);
    }

    public function testPaymentMethodIsEmpty(): void
    {
        $this->expectException(OrderConstraintException::class);
        $this->expectExceptionMessage('The selected payment method is invalid.');
        new AddPaymentCommand(1, date('Y-m-d'), '', 0, 2);
    }

    /**
     * @dataProvider getInvalidChars
     *
     * @param string $invalidChar
     */
    public function testPaymentMethodWithInvalidCharacters(string $invalidChar): void
    {
        $this->expectException(OrderConstraintException::class);
        $this->expectExceptionMessage('The selected payment method is invalid.');
        new AddPaymentCommand(1, date('Y-m-d'), $invalidChar . 'Check', 0, 2);
    }

    public function getInvalidChars(): iterable
    {
        foreach (str_split(AddPaymentCommand::INVALID_CHARACTERS_NAME) as $invalidChar) {
            yield [$invalidChar];
        }
    }

    public function testConstruct(): void
    {
        $instance = new AddPaymentCommand(1, date('Y-m-d'), 'Check', 0, 2);

        $this->assertEquals(1, $instance->getOrderId()->getValue());
        $this->assertEquals(date('Y-m-d'), $instance->getPaymentDate()->format('Y-m-d'));
        $this->assertEquals('Check', $instance->getPaymentMethod());
        $this->assertEquals('0', $instance->getPaymentAmount()->__toString());
        $this->assertEquals(2, $instance->getPaymentCurrencyId()->getValue());
        $this->assertNull($instance->getOrderInvoiceId());
        $this->assertNull($instance->getPaymentTransactionId());
    }

    public function testConstructWithOrderInvoiceId(): void
    {
        $instance = new AddPaymentCommand(1, date('Y-m-d'), 'Check', 0, 2, 3);

        $this->assertEquals(1, $instance->getOrderId()->getValue());
        $this->assertEquals(date('Y-m-d'), $instance->getPaymentDate()->format('Y-m-d'));
        $this->assertEquals('Check', $instance->getPaymentMethod());
        $this->assertEquals('0', $instance->getPaymentAmount()->__toString());
        $this->assertEquals(2, $instance->getPaymentCurrencyId()->getValue());
        $this->assertEquals(3, $instance->getOrderInvoiceId());
        $this->assertNull($instance->getPaymentTransactionId());
    }

    public function testConstructWithTransactionId(): void
    {
        $instance = new AddPaymentCommand(1, date('Y-m-d'), 'Check', 0, 2, 3, 'TransactionId');

        $this->assertEquals(1, $instance->getOrderId()->getValue());
        $this->assertEquals(date('Y-m-d'), $instance->getPaymentDate()->format('Y-m-d'));
        $this->assertEquals('Check', $instance->getPaymentMethod());
        $this->assertEquals('0', $instance->getPaymentAmount()->__toString());
        $this->assertEquals(2, $instance->getPaymentCurrencyId()->getValue());
        $this->assertEquals(3, $instance->getOrderInvoiceId());
        $this->assertEquals('TransactionId', $instance->getPaymentTransactionId());
    }
}
