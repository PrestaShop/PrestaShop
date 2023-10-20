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
declare(strict_types=1);

namespace Tests\Unit\Core\Util\String;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\String\ModifyAllShopsUtil;

class ModifyAllShopsUtilTest extends TestCase
{
    /**
     * @dataProvider getTestPrefixFieldPathData
     *
     * @param string $fieldPath
     * @param string $prefix
     * @param string $expectedResult
     */
    public function testPrefixFieldPath(string $fieldPath, string $prefix, string $expectedResult): void
    {
        self::assertSame($expectedResult, ModifyAllShopsUtil::prefixFieldPathWithAllShops($fieldPath, $prefix));
    }

    public function getTestPrefixFieldPathData(): iterable
    {
        yield [
            '',
            'what',
            '',
        ];

        yield [
            '[foo][bar]',
            'hello',
            '[foo][hellobar]',
        ];

        yield [
            '[stock][delta_quantity][delta]',
            'modify_all_shops_',
            '[stock][delta_quantity][modify_all_shops_delta]',
        ];
    }
}
