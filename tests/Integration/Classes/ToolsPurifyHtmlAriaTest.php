<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use PHPUnit\Framework\TestCase;
use Tools;

class ToolsPurifyHtmlAriaTest extends TestCase
{
    /**
     * Tools::purifyHTML() returns its input untouched when the purifier is switched off, which would make
     * every assertion below pass without the purifier ever running. Fail loudly instead.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (!Configuration::get('PS_USE_HTMLPURIFIER')) {
            $this->fail('PS_USE_HTMLPURIFIER is off, so Tools::purifyHTML() is a passthrough and this test would assert nothing.');
        }
    }

    /**
     * @dataProvider provideKeptAttributes
     */
    public function testAriaAttributesAndHreflangSurvivePurification(string $expected, string $html): void
    {
        $purified = Tools::purifyHTML($html);

        $this->assertStringContainsString(
            $expected,
            $purified,
            sprintf('%s was dropped by HTML Purifier. Purified output: %s', $expected, $purified)
        );
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideKeptAttributes(): iterable
    {
        yield 'aria-label' => ['aria-label="Open the cart"', '<a href="/cart" aria-label="Open the cart">Cart</a>'];
        yield 'aria-labelledby' => ['aria-labelledby="related-title"', '<div aria-labelledby="related-title"><h2 id="related-title">Related</h2></div>'];
        yield 'aria-describedby' => ['aria-describedby="size-note"', '<p aria-describedby="size-note">Sizes</p><p id="size-note">Note</p>'];
        yield 'aria-details' => ['aria-details="chart-data"', '<img src="/img/chart.png" alt="Chart" aria-details="chart-data" /><div id="chart-data">Data</div>'];
        yield 'aria-hidden' => ['aria-hidden="true"', '<p><span aria-hidden="true">*</span> Required</p>'];
        yield 'aria-current' => ['aria-current="page"', '<ul><li><a href="/page" aria-current="page">Page</a></li></ul>'];
        yield 'role' => ['role="note"', '<div role="note">Text</div>'];
        yield 'hreflang' => ['hreflang="de"', '<a href="/de/" hreflang="de">Deutsch</a>'];
    }

    /**
     * Keeping these attributes must not widen what else may be put on an element. These cases pass with and
     * without the change, which is what makes them controls.
     *
     * @dataProvider provideRemovedMarkup
     */
    public function testOtherMarkupIsStillRemoved(string $needle, string $html): void
    {
        $purified = Tools::purifyHTML($html);

        $this->assertStringNotContainsString(
            $needle,
            $purified,
            sprintf('"%s" survived purification. Purified output: %s', $needle, $purified)
        );
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideRemovedMarkup(): iterable
    {
        yield 'event handler next to an ARIA attribute' => ['onclick', '<a href="/page" aria-label="Page" onclick="alert(1)">Page</a>'];
        yield 'javascript: link with hreflang' => ['javascript:', '<a href="javascript:alert(1)" hreflang="en">Link</a>'];
        yield 'hreflang that is not a language code' => ['hreflang', '<a href="/page" hreflang="not a language">Link</a>'];
        yield 'aria-hidden with an invalid value' => ['aria-hidden', '<span aria-hidden="maybe">Text</span>'];
        yield 'aria-current with an invalid value' => ['aria-current', '<a href="/page" aria-current="always">Page</a>'];
        yield 'quotes in an ARIA value do not open a new attribute' => ['onmouseover="', '<a href="/page" aria-label="a&quot; onmouseover=&quot;alert(1)">Page</a>'];
        yield 'widget state' => ['aria-expanded', '<div aria-expanded="true">Text</div>'];
        yield 'data attribute' => ['data-toggle', '<div data-toggle="modal">Text</div>'];
    }

    /**
     * Markup without the new attributes is purified exactly as before the change (expected values recorded on
     * the base branch). Declaring an attribute with a shared type name such as 'Text' would fail the image
     * cases: the purifier would then require the attribute on img and remove every img without it.
     *
     * @dataProvider provideUnrelatedMarkup
     */
    public function testUnrelatedMarkupIsPurifiedAsBefore(string $expected, string $html): void
    {
        $this->assertSame($expected, Tools::purifyHTML($html));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideUnrelatedMarkup(): iterable
    {
        yield 'image' => ['<img src="/img/a.png" alt="A" />', '<img src="/img/a.png" alt="A" />'];
        yield 'image with size and title' => [
            '<img src="/img/b.png" alt="" width="100" height="50" title="B" />',
            '<img src="/img/b.png" alt="" width="100" height="50" title="B" />',
        ];
        yield 'table' => [
            '<table class="specs"><thead><tr><th scope="col">Size</th></tr></thead><tbody><tr><td>40</td></tr></tbody></table>',
            '<table class="specs"><thead><tr><th scope="col">Size</th></tr></thead><tbody><tr><td>40</td></tr></tbody></table>',
        ];
        yield 'link with target and rel' => [
            '<a href="/page" target="_blank" rel="nofollow noreferrer noopener">Page</a>',
            '<a href="/page" target="_blank" rel="nofollow">Page</a>',
        ];
        yield 'video with source' => [
            '<video controls="" width="320"><source src="/media/a.mp4" type="video/mp4"></source></video>',
            '<video controls="controls" width="320"><source src="/media/a.mp4" type="video/mp4" /></video>',
        ];
        yield 'paragraph with style' => [
            '<p style="color:#ff0000;text-align:center;">Text</p>',
            '<p style="color:#ff0000;text-align:center;">Text</p>',
        ];
        yield 'heading with core attributes' => [
            '<h2 id="intro" class="title" lang="en" dir="ltr" title="Intro" xml:lang="en">Intro</h2>',
            '<h2 id="intro" class="title" lang="en" dir="ltr" title="Intro">Intro</h2>',
        ];
    }
}
