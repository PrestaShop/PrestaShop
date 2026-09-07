<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Smarty;

use Configuration;
use Context;
use Currency;
use Language;
use Link;
use PHPUnit\Framework\TestCase;
use Shop;

/**
 * `preload.tpl` is written by webpack into the gitignored `public/` directory of each admin theme, so an
 * install whose admin assets were never built - a plain checkout, or `docker compose up` on one - does
 * not have it. `header.tpl` included it unconditionally, and Smarty answers a missing include with an
 * exception, so the whole back office went down over an optional set of preload hints.
 *
 * @group non-mysql
 */
class AdminHeaderPreloadTest extends TestCase
{
    /**
     * @var array<string, string|null> path => original contents, or null when the file was absent
     */
    private $movedAside = [];

    protected function setUp(): void
    {
        parent::setUp();

        foreach ($this->preloadPaths() as $path) {
            $this->movedAside[$path] = file_exists($path) ? (string) file_get_contents($path) : null;
            if (null !== $this->movedAside[$path]) {
                unlink($path);
            }
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->movedAside as $path => $contents) {
            if (null === $contents) {
                if (file_exists($path)) {
                    unlink($path);
                }
            } else {
                file_put_contents($path, $contents);
            }
        }
        $this->movedAside = [];

        parent::tearDown();
    }

    /**
     * @dataProvider adminThemeProvider
     */
    public function testTheHeaderRendersWhenTheBuiltPreloadFileIsMissing(string $theme): void
    {
        $header = _PS_ADMIN_DIR_ . '/themes/' . $theme . '/template/header.tpl';
        $this->assertFileExists($header);
        $this->assertFileDoesNotExist(_PS_ADMIN_DIR_ . '/themes/' . $theme . '/public/preload.tpl');

        $smarty = $this->prepareContext()->smarty;
        $smarty->clearCompiledTemplate();

        $output = $smarty->fetch($header);

        $this->assertNotSame('', trim($output), 'the header must still render without the preload hints');
    }

    /**
     * @dataProvider adminThemeProvider
     */
    public function testTheHeaderStillEmitsThePreloadHintsWhenTheFileIsThere(string $theme): void
    {
        $path = _PS_ADMIN_DIR_ . '/themes/' . $theme . '/public/preload.tpl';
        file_put_contents($path, '<link rel="preload" href="{$admin_dir}marker.woff2" as="font">');

        $smarty = $this->prepareContext()->smarty;
        $smarty->clearCompiledTemplate();

        $output = $smarty->fetch(_PS_ADMIN_DIR_ . '/themes/' . $theme . '/template/header.tpl');

        $this->assertStringContainsString('marker.woff2', $output, 'the hints must still reach the page');
        $this->assertStringContainsString(
            'themes/' . $theme . '/public/',
            $output,
            'admin_dir must still be handed to the included template'
        );
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function adminThemeProvider(): array
    {
        return [['new-theme'], ['default']];
    }

    private function prepareContext(): Context
    {
        $context = Context::getContext();
        $context->link = new Link();
        $context->shop = new Shop(1);
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->smarty->assign([
            'link' => $context->link,
            'js_def' => [],
            'js_files' => [],
            'css_files' => [],
        ]);

        return $context;
    }

    /**
     * @return array<int, string>
     */
    private function preloadPaths(): array
    {
        return [
            _PS_ADMIN_DIR_ . '/themes/new-theme/public/preload.tpl',
            _PS_ADMIN_DIR_ . '/themes/default/public/preload.tpl',
        ];
    }
}
