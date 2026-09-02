<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
