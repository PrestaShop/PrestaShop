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

class ToolsPurifyHtml5Test extends TestCase
{
    /**
     * Tools::purifyHTML() returns its input untouched when the purifier is switched off, which would make
     * every assertion below pass without the purifier ever running. Fail loudly instead of vacuously.
     *
     * Note purifyHTML() caches both the flag and the purifier in statics, so this reads the value that the
     * rest of the process will use, not merely the current configuration row.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (!Configuration::get('PS_USE_HTMLPURIFIER')) {
            $this->fail('PS_USE_HTMLPURIFIER is off, so Tools::purifyHTML() is a passthrough and this test would assert nothing.');
        }
    }

    /**
     * @dataProvider provideHtml5Elements
     */
    public function testHtml5ElementsSurvivePurification(string $tag, string $html): void
    {
        $purified = Tools::purifyHTML($html);

        $this->assertStringContainsString(
            '<' . $tag,
            $purified,
            sprintf('The <%s> element was dropped by HTML Purifier. Purified output: %s', $tag, $purified)
        );
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public function provideHtml5Elements(): iterable
    {
        // Sectioning and grouping content.
        yield 'section' => ['section', '<section class="intro"><p>text</p></section>'];
        yield 'article' => ['article', '<article class="post"><p>text</p></article>'];
        yield 'aside' => ['aside', '<aside><p>text</p></aside>'];
        yield 'nav' => ['nav', '<nav><a href="/page">link</a></nav>'];
        yield 'header' => ['header', '<header><h1>title</h1></header>'];
        yield 'footer' => ['footer', '<footer><p>text</p></footer>'];
        yield 'main' => ['main', '<main><p>text</p></main>'];
        yield 'figure' => ['figure', '<figure><img src="/img/a.png" alt="a" /></figure>'];
        yield 'figcaption' => ['figcaption', '<figure><figcaption>caption</figcaption></figure>'];

        // Text-level semantics.
        yield 'mark' => ['mark', '<p><mark>highlighted</mark></p>'];
        yield 'bdi' => ['bdi', '<p><bdi>name</bdi></p>'];
        yield 'time' => ['time', '<p><time datetime="2026-01-01">January</time></p>'];
        yield 'data' => ['data', '<p><data value="42">forty two</data></p>'];
        yield 'wbr' => ['wbr', '<p>long<wbr />word</p>'];

        // Interactive content.
        yield 'details' => ['details', '<details open="open"><summary>more</summary><p>text</p></details>'];
        yield 'summary' => ['summary', '<details><summary>more</summary><p>text</p></details>'];

        // Media, completing the video/source pair the definition already carried.
        yield 'audio' => ['audio', '<audio src="/media/a.mp3" controls="controls"></audio>'];
        yield 'picture' => ['picture', '<picture><img src="/img/a.png" alt="a" /></picture>'];
        yield 'track' => ['track', '<video src="/media/a.mp4"><track src="/media/a.vtt" kind="captions" /></video>'];
    }

    /**
     * Declaring the elements above must not widen what may be put ON them. Without this, a regression that
     * relaxed attribute handling would still leave every assertion above green.
     *
     * @dataProvider provideMarkupThatMustBeSanitized
     */
    public function testDeclaringHtml5ElementsDoesNotAllowScriptingAttributes(string $needle, string $html): void
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
    public function provideMarkupThatMustBeSanitized(): iterable
    {
        yield 'onclick on a new sectioning element' => ['onclick', '<section onclick="alert(1)">text</section>'];
        yield 'onerror on a new sectioning element' => ['onerror', '<article onerror="alert(1)">text</article>'];
        yield 'onmouseover on a new inline element' => ['onmouseover', '<mark onmouseover="alert(1)">text</mark>'];
        yield 'javascript: href inside a new element' => ['javascript:', '<nav><a href="javascript:alert(1)">link</a></nav>'];
        yield 'javascript: source on a new media element' => ['javascript:', '<audio src="javascript:alert(1)"></audio>'];
    }
}
