<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Smarty;

use PHPUnit\Framework\TestCase;

class SmartySettingsTest extends TestCase
{
    private $smarty;

    protected function setUp(): void
    {
        parent::setUp();

        global $smarty;
        $this->smarty = $smarty;
        $this->smarty->force_compile = true;
        $this->smarty->escape_html = true;
    }

    private function render(string $templateString, array $parameters): string
    {
        return $this->smarty
            ->assign($parameters)
            ->fetch('string:' . $templateString);
    }

    private function escapeTemplateLocationComments(string $string): string
    {
        return preg_replace('/\\n<!--(.|\s)*?-->\\n/', '', $string);
    }

    public function testALinkIsEscapedAutomatically(): void
    {
        $str = '<a>hello</a>';
        $this->assertEquals(
            '&lt;a&gt;hello&lt;/a&gt;',
            $this->escapeTemplateLocationComments(
                $this->render('{$str}', ['str' => $str])
            )
        );
    }

    public function testNofilterPreventsEscape(): void
    {
        $str = '<a>hello</a>';
        $this->assertEquals(
            $str,
            $this->escapeTemplateLocationComments(
                $this->render('{$str nofilter}', ['str' => $str])
            )
        );
    }

    public function testHtmlIsNotEscapedTwice(): void
    {
        $str = '<a>hello</a>';
        $this->assertEquals(
            '&lt;a&gt;hello&lt;/a&gt;',
            $this->escapeTemplateLocationComments(
                $this->render('{$str|escape:"html"}', ['str' => $str])
            )
        );
    }
}
