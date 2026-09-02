<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
