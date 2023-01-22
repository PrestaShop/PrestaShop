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

namespace Tests\Unit\Core\Category\NameBuilder;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Core\Category\NameBuilder\CategoryDisplayNameBuilder;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class CategoryDisplayNameBuilderTest extends TestCase
{
    /**
     * @dataProvider getDataForTestBuildsDisplayNames
     *
     * @param CategoryDisplayNameBuilder $builder
     * @param string $categoryName
     * @param int $categoryId
     * @param string $expectedResult
     */
    public function testBuildsDisplayNames(
        CategoryDisplayNameBuilder $builder,
        string $categoryName,
        int $categoryId,
        string $expectedResult
    ): void {
        $actualResult = $builder->build(
            $categoryName,
            new ShopId(1),
            new LanguageId(1),
            new CategoryId($categoryId),
            // cannot use cache because legacy Cache is not found by units
            false
        );

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return iterable
     */
    public function getDataForTestBuildsDisplayNames(): iterable
    {
        $categoryRepositoryMock = $this->mockRepository([]);
        $separator = ' > ';
        $builder = new CategoryDisplayNameBuilder($categoryRepositoryMock, $separator);

        yield [
            $builder,
            'Home',
            2,
            'Home',
        ];

        $categoryRepositoryMock = $this->mockRepository([2 => ['Home'], 3 => ['Home']]);
        $separator = ' > ';
        $builder = new CategoryDisplayNameBuilder($categoryRepositoryMock, $separator);

        yield [
            $builder,
            'Home',
            2,
            'Home (#2)',
        ];

        $categoryRepositoryMock = $this->mockRepository([
            2 => ['Home'],
            4 => ['Home', 'Accessories'],
            5 => ['Clothes', 'Accessories'],
        ]);
        $separator = ' > ';
        $builder = new CategoryDisplayNameBuilder($categoryRepositoryMock, $separator);

        yield [
            $builder,
            'Accessories',
            4,
            'Home > Accessories',
        ];

        $categoryRepositoryMock = $this->mockRepository([
            2 => ['Home'],
            4 => ['Home', 'Accessories'],
            5 => ['Clothes', 'Accessories'],
            6 => ['Clothes', 'Accessories', 'Accessories'],
        ]);
        $separator = ' > ';
        $builder = new CategoryDisplayNameBuilder($categoryRepositoryMock, $separator);

        yield [
            $builder,
            'Accessories',
            6,
            'Accessories > Accessories',
        ];

        $categoryRepositoryMock = $this->mockRepository([
            2 => ['Home'],
            4 => ['Home', 'Accessories'],
            5 => ['Clothes', 'Accessories'],
            6 => ['Clothes', 'Accessories'],
            7 => ['Clothes', 'Accessories', 'Accessories'],
        ]);
        $separator = ' > ';
        $builder = new CategoryDisplayNameBuilder($categoryRepositoryMock, $separator);

        yield [
            $builder,
            'Accessories',
            6,
            'Clothes > Accessories (#6)',
        ];

        $categoryRepositoryMock = $this->mockRepository([
            2 => ['Home'],
            3 => ['Home', 'Clothes'],
            6 => ['Home', 'Clothes', 'Home'],
            7 => ['Home', 'Clothes', 'Home', 'Clothes'],
        ]);
        $separator = ' > ';
        $builder = new CategoryDisplayNameBuilder($categoryRepositoryMock, $separator);

        yield [
            $builder,
            'Clothes',
            7,
            'Clothes > Home > Clothes',
        ];

        $separator = ' + ';
        $builder = new CategoryDisplayNameBuilder($categoryRepositoryMock, $separator);

        yield [
            $builder,
            'Clothes',
            7,
            'Clothes + Home + Clothes',
        ];
    }

    /**
     * @param array<int, string[]> $breadcrumbPartsByDuplicatedIds
     *
     * @return CategoryRepository
     */
    private function mockRepository(array $breadcrumbPartsByDuplicatedIds): CategoryRepository
    {
        $mock = $this->getMockBuilder(CategoryRepository::class)
            ->onlyMethods([
                'getDuplicateNameIds',
                'getBreadcrumbParts',
            ])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mock->method('getDuplicateNameIds')
            ->willReturn(array_map(function (int $id): CategoryId {
                return new CategoryId($id);
            }, array_keys($breadcrumbPartsByDuplicatedIds)))
        ;

        $mock->method('getBreadcrumbParts')
            ->willReturnCallback(
                function (CategoryId $categoryId, LanguageId $languageId) use ($breadcrumbPartsByDuplicatedIds): array {
                    return $breadcrumbPartsByDuplicatedIds[$categoryId->getValue()];
                }
            )
        ;

        return $mock;
    }
}
