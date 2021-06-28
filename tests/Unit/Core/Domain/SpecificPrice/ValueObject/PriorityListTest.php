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

namespace Tests\Unit\Core\Domain\SpecificPrice\ValueObject;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;

class PriorityListTest extends TestCase
{
    /**
     * @dataProvider getValidPriorities
     *
     * @param string[] $priorities
     */
    public function testCreatesObjectWithValidPriorities(array $priorities): void
    {
        $priorityList = new PriorityList($priorities);

        self::assertEquals($priorityList->getPriorities(), $priorities);
    }

    /**
     * @dataProvider getInvalidPriorities
     *
     * @param string[] $priorities
     */
    public function testThrowsExceptionWhenInvalidAreProvided(array $priorities): void
    {
        $this->expectException(SpecificPriceConstraintException::class);
        $this->expectExceptionCode(SpecificPriceConstraintException::INVALID_PRIORITY);

        new PriorityList($priorities);
    }

    /**
     * @dataProvider getDuplicatePriorities
     *
     * @param string[] $priorities
     */
    public function testThrowsExceptionWhenDuplicatePrioritiesAreProvided(array $priorities): void
    {
        $this->expectException(SpecificPriceConstraintException::class);
        $this->expectExceptionCode(SpecificPriceConstraintException::DUPLICATE_PRIORITY);

        new PriorityList($priorities);
    }

    /**
     * @return Generator
     */
    public function getValidPriorities(): Generator
    {
        yield [
            ['id_country', 'id_currency', 'id_group', 'id_shop'],
            ['id_currency', 'id_country', 'id_group', 'id_shop'],
            ['id_group', 'id_currency', 'id_country', 'id_shop'],
            ['id_shop', 'id_currency', 'id_group', 'id_country'],
            ['id_currency', 'id_shop', 'id_group', 'id_country'],
        ];
    }

    /**
     * @return Generator
     */
    public function getInvalidPriorities(): Generator
    {
        yield [
            ['id_random', 'id_currency', 'id_group', 'id_shop'],
            ['id_country', 'id_currency', 'id_group', 'id_GROUP'],
        ];
    }

    /**
     * @return Generator
     */
    public function getDuplicatePriorities(): Generator
    {
        yield [
            ['id_currency', 'id_currency', 'id_group', 'id_shop'],
            ['id_country', 'id_shop', 'id_group', 'id_group'],
        ];
    }
}
