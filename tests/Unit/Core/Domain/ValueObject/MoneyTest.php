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

namespace Tests\Unit\Core\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;

class MoneyTest extends TestCase
{
    /**
     * @dataProvider getValidDataForCreatingMoneyClass
     *
     * @param string $number
     * @param int $currencyId
     * @param bool $taxIncluded
     *
     * @return void
     */
    public function testItCreatesMoneyClass(
        string $number,
        int $currencyId,
        bool $taxIncluded
    ): void {
        $money = new Money(new DecimalNumber($number), new CurrencyId($currencyId), $taxIncluded);

        $this->assertTrue($money->getAmount()->equals(new DecimalNumber($number)));
        $this->assertSame('', $money->getAmount()->getSign());
        $this->assertSame($currencyId, $money->getCurrencyId()->getValue());
        $this->assertSame($taxIncluded, $money->isTaxIncluded());
    }

    public function getValidDataForCreatingMoneyClass(): iterable
    {
        yield ['100', 10, true];
        yield ['100.5', 5, false];
        yield ['0', 99, true];
    }

    public function testItFailsWhenCreatingWithNegativeValue()
    {
        $this->expectException(DomainConstraintException::class);
        $this->expectExceptionCode(DomainConstraintException::INVALID_MONEY_AMOUNT);
        $this->expectExceptionMessage('Money amount cannot be lower than zero, -100.000000 given');

        new Money(new DecimalNumber('-100'), new CurrencyId(10), false);
    }
}
