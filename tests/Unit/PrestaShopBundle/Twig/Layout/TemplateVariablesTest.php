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
            'shipped language TinyMCE knows' => ['fr', 'fr'],
            // js/tiny_mce/langs carries no file for these, they are shipped under install-dev/langs
            'shipped language TinyMCE does not know' => ['tw', 'en'],
            'Norwegian, reported in the issue' => ['no', 'en'],
            'unknown iso code' => ['zz', 'en'],
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
