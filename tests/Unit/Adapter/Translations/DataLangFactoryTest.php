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

namespace Tests\Unit\Adapter\Translations;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\EntityTranslation\DataLangFactory;
use PrestaShopBundle\Translation\TranslatorInterface;

class DataLangFactoryTest extends TestCase
{
    /**
     * @dataProvider provideTableNames
     */
    public function testItCreatesClassNamesFromTableNames(string $databasePrefix, string $tableName, string $expected)
    {
        $factory = new DataLangFactory(
            $databasePrefix,
            $this->getMockBuilder(TranslatorInterface::class)->getMock()
        );
        $this->assertSame($expected, $factory->getClassNameFromTable($tableName));
    }

    public function provideTableNames()
    {
        return [
            ['ps_', 'ps_tab_lang', 'TabLang'],
            ['ps_', 'ps_cart_rule_lang', 'CartRuleLang'],
            ['ps_', 'cart_rule_lang', 'CartRuleLang'],
            ['ps_', 'tab', 'TabLang'],
            ['ps', 'pstab_lang', 'TabLang'],
            ['ps', 'ps_tab_lang', 'TabLang'],
        ];
    }
}
