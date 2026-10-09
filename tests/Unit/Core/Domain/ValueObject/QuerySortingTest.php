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

namespace Core\Domain\ValueObject;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Exception\InvalidSortingException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\QuerySorting;

class QuerySortingTest extends TestCase
{
    /**
     * @dataProvider getValidValuesForClassCreation
     *
     * @param array $sortingFields
     * @param string $sortingOrder
     *
     * @throws InvalidSortingException
     */
    public function testItCreatesClassWithValidValues(array $sortingFields, string $sortingOrder): void
    {
        $querySorting = new QuerySorting($sortingFields, $sortingOrder);

        Assert::assertEquals($querySorting->getSortingFields(), $sortingFields);
        Assert::assertEquals($querySorting->getSortingOrder(), $sortingOrder);
    }

    /**
     * @dataProvider getInvalidSortingFields
     *
     * @param array $sortingFields
     *
     * @throws InvalidSortingException
     */
    public function testItThrowsExceptionWhenInvalidSortingFieldsIsProvided(array $sortingFields): void
    {
        $this->expectException(InvalidSortingException::class);
        new QuerySorting($sortingFields, QuerySorting::ASC);
    }

    /**
     * @dataProvider getInvalidSortingOrder
     *
     * @param string $sortingOrder
     */
    public function testItThrowsExceptionWhenInvalidSortingOrderIsProvided(string $sortingOrder): void
    {
        $this->expectException(InvalidSortingException::class);
        new QuerySorting(['date_add'], $sortingOrder);
    }

    /**
     * @return Generator
     */
    public function getInvalidSortingOrder(): Generator
    {
        yield ['BAD_VALUE'];
    }

    /**
     * @return Generator
     */
    public function getInvalidSortingFields(): Generator
    {
        yield [['bad_value']];
        yield [['date_add', 'bad_value']];
    }

    /**
     * @return Generator
     */
    public function getValidValuesForClassCreation(): Generator
    {
        yield [['id_product'], 'ASC'];
        yield [['date_add', 'product_reference'], 'ASC'];
        yield [['id_product', 'product_supplier_reference', 'product_reference'], 'DESC'];
    }
}
