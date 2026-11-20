<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\assets;

use CssMinifier;
use PHPUnit\Framework\TestCase;

/**
 * The combined stylesheet is written to a different directory than the sources, so every relative url() is
 * rewritten to keep resolving. A fragment-only url(#gooey) is not a path — it points at an SVG filter, clip
 * path or mask inlined in the page — and rewriting it breaks the rule.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/33329
 */
class CssMinifierTest extends TestCase
{
    private string $workDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workDir = sys_get_temp_dir() . '/ps-css-minifier-' . uniqid();
        mkdir($this->workDir . '/src', 0777, true);
        mkdir($this->workDir . '/cache', 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->workDir . '/*/*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($this->workDir . '/src');
        @rmdir($this->workDir . '/cache');
        @rmdir($this->workDir);
        parent::tearDown();
    }

    /**
     * @dataProvider provideUrls
     */
    public function testUrlsSurviveMinificationAsExpected(string $rule, string $expected, string $because): void
    {
        $this->assertStringContainsString($expected, $this->minify($rule), $because);
    }

    public function provideUrls(): array
    {
        return [
            'fragment-only filter is left alone' => [
                '#heading{filter:url("#gooey")}',
                'url("#gooey")',
                'A fragment points at an SVG inlined in the page, not at a file.',
            ],
            'unquoted fragment-only filter is left alone' => [
                '.b{filter:url(#svg-blur)}',
                'url(#svg-blur)',
                'Quoting must not change whether the fragment is treated as a path.',
            ],
            'fragment-only clip-path is left alone' => [
                '.c{clip-path:url(#clipper)}',
                'url(#clipper)',
                'clip-path takes the same fragment references as filter.',
            ],
            'fragment-only mask is left alone' => [
                '.m{mask:url(#masker)}',
                'url(#masker)',
                'mask takes the same fragment references as filter.',
            ],
            'a file with a fragment is still rewritten' => [
                '.f{filter:url(my-file.svg#svg-blur)}',
                'url(../src/my-file.svg#svg-blur)',
                'This one carries a real path, so it must keep resolving from the cache directory.',
            ],
            'an ordinary relative url is still rewritten' => [
                '.bg{background:url(../img/logo.png)}',
                'url(../img/logo.png)',
                'Ordinary relative paths must keep being rewritten.',
            ],
            'a root-relative url is left alone' => [
                '.a{background:url(/img/abs.png)}',
                'url(/img/abs.png)',
                'Root-relative paths already resolve.',
            ],
            'a remote url is left alone' => [
                '.r{background:url(https://example.com/x.png)}',
                'url(https://example.com/x.png)',
                'Remote paths already resolve.',
            ],
        ];
    }

    private function minify(string $css): string
    {
        $source = $this->workDir . '/src/theme.css';
        $destination = $this->workDir . '/cache/theme-ccc.css';
        file_put_contents($source, $css);

        CssMinifier::minify([$source], $destination);

        return (string) file_get_contents($destination);
    }
}
