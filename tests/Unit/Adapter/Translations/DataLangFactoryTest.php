<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Adapter\Translations;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\EntityTranslation\DataLangFactory;

class DataLangFactoryTest extends TestCase
{
    /**
     * @param string $tableName
     * @param string $expected
     *
     * @dataProvider provideTableNames
     */
    public function testItCreatesClassNamesFromTableNames(string $tableName, string $expected)
    {
        $factory = new DataLangFactory(_DB_PREFIX_);
        $this->assertSame($expected, $factory->getClassNameFromTable($tableName));
    }

    public function provideTableNames()
    {
        return [
            [_DB_PREFIX_ . 'tab_lang', 'TabLang'],
            [_DB_PREFIX_ . 'cart_rule_lang', 'CartRuleLang'],
            ['cart_rule_lang', 'CartRuleLang'],
            [_DB_PREFIX_ . 'tab_lang', 'TabLang'],
            ['tab', 'TabLang'],
        ];
    }
}
