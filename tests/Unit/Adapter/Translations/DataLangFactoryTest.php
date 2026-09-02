<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
