<?php
/*
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

namespace Tests\Unit\Core\Domain\Customer\Group\Command;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupConstraintException;

class AddCustomerGroupCommandTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     *
     * @param DecimalNumber $reductionPercent
     */
    public function testItWorksWhenProvidingValidReduction(DecimalNumber $reductionPercent): void
    {
        new AddCustomerGroupCommand(
            ['toto', 'tata'],
            $reductionPercent,
            false,
            true,
            [1]
        );
        $this->assertTrue(true);
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param DecimalNumber $reductionPercent
     */
    public function testItThrowsExceptionWhenProvidingInvalidReduction(DecimalNumber $reductionPercent): void
    {
        $this->expectException(GroupConstraintException::class);

        new AddCustomerGroupCommand(
            ['toto', 'tata'],
            $reductionPercent,
            false,
            true,
            [1]
        );
    }

    /**
     * @return Generator
     */
    public function getValidValues(): Generator
    {
        yield [new DecimalNumber('-0.00')];
        yield [new DecimalNumber('-0')];
        yield [new DecimalNumber('0')];
        yield [new DecimalNumber('0.001')];
        yield [new DecimalNumber('0.01')];
        yield [new DecimalNumber('1.23')];
        yield [new DecimalNumber('12.34')];
        yield [new DecimalNumber('99.99')];
        yield [new DecimalNumber('99.999')];
        yield [new DecimalNumber('100')];
        yield [new DecimalNumber('100.0')];
        yield [new DecimalNumber('100.00')];
    }

    /**
     * @return Generator
     */
    public function getInvalidValues(): Generator
    {
        yield [new DecimalNumber('-0.001')];
        yield [new DecimalNumber('-0.01')];
        yield [new DecimalNumber('100.01')];
        yield [new DecimalNumber('100.001')];
    }
}
