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

namespace Tests\Unit\Core\Localization\Number;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Number\LocaleNumberTransformer;

class LocaleNumberTransformerTest extends TestCase
{
    /**
     * Instantiate a LocaleNumberTransformer with custom locale.
     *
     * @param string $localeCode
     *
     * @return LocaleNumberTransformer
     */
    protected function createTransformer(string $localeCode): LocaleNumberTransformer
    {
        $locale = $this->createMock(Locale::class);
        $locale->method('getCode')->willReturn($localeCode);

        return new LocaleNumberTransformer($locale);
    }

    /**
     * Test transformer
     *
     * @param string $localeCode
     * @param string $expectedLocale
     *
     * @return void
     *
     * @dataProvider provideTransformerTest
     */
    public function testTransformer(string $localeCode, string $expectedLocale): void
    {
        $transformer = $this->createTransformer($localeCode);
        $this->assertEquals($expectedLocale, $transformer->getLocaleForNumberInputs());
    }

    /**
     * Provide data for transformer test.
     *
     * @return array[]
     */
    public function provideTransformerTest(): array
    {
        return [
            ['ar_SA', 'en'],
            ['fr_FR', 'fr'],
            ['en_US', 'en'],
            ['bn_BN', 'en'],
            ['es_ES', 'es'],
            ['fa_FA', 'en'],
            ['it_IT', 'it'],
        ];
    }
}
