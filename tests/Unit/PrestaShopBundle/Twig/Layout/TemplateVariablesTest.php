<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig\Layout;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Twig\Layout\TemplateVariables;

class TemplateVariablesTest extends TestCase
{
    /**
     * @dataProvider getIsoCodes
     */
    public function testTheEditorLanguageFallsBackWhenTinyMceHasNoFileForIt(string $isoUser, string $expected): void
    {
        $this->assertSame($expected, $this->buildFor($isoUser)->getIsoUserForEditor());
    }

    public function getIsoCodes(): array
    {
        return [
            'shipped language TinyMCE knows under the same name' => ['fr', 'fr'],
            // TinyMCE carries these, under a different name than the iso PrestaShop ships
            'Traditional Chinese' => ['tw', 'zh_TW'],
            'Norwegian, reported in #13131 and #10012' => ['no', 'nb'],
            'Mexican Spanish' => ['mx', 'es_MX'],
            'Vietnamese' => ['vn', 'vi'],
            // no French Canadian file, the closest one TinyMCE has is French
            'Quebec French' => ['qc', 'fr'],
            // TinyMCE has nothing for these at all
            'Hindi' => ['hi', 'en'],
            'Albanian' => ['sq', 'en'],
            'unknown iso code' => ['zz', 'en'],
        ];
    }

    /**
     * @dataProvider getLanguageCodes
     */
    public function testTheMomentLanguageCodeIsMappedOnlyWhenMomentCannotResolveItItself(
        string $isoUser,
        string $fullLanguageCode,
        string $expected
    ): void {
        $this->assertSame($expected, $this->buildFor($isoUser)->getMomentLanguageCode($fullLanguageCode));
    }

    public function getLanguageCodes(): array
    {
        return [
            // moment walks "xx-yy" down to "xx" on its own, so these must be passed through untouched
            'moment resolves the region code itself' => ['bg', 'bg-bg', 'bg-bg'],
            'moment resolves en-us itself' => ['en', 'en-us', 'en-us'],
            'moment resolves vi-vn itself' => ['vn', 'vi-vn', 'vi-vn'],
            // moment carries these under a different name
            'Traditional Chinese, reported in the issue' => ['tw', 'tw-tw', 'zh-tw'],
            'Norwegian, reported in the issue' => ['no', 'no', 'nb'],
            'French Quebec' => ['qc', 'qc', 'fr-ca'],
            // mapped on the iso when the language code carries no region
            'Vietnamese by iso alone' => ['vn', 'vn', 'vi'],
        ];
    }

    private function buildFor(string $isoUser): TemplateVariables
    {
        return new TemplateVariables(
            $isoUser,
            false,
            'AdminProducts',
            false,
            false,
            [],
            false,
            false,
            '9.2.0',
            null,
            false,
            false,
            false,
            'http://localhost/'
        );
    }
}
