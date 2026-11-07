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

class ToolsPurifyHtmlAllowStyleTest extends TestCase
{
    private const MARKUP = '<style type="text/css">.t{color:red}</style><p>text</p>';

    /**
     * @var string|bool
     */
    private $initialPurifierSetting;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initialPurifierSetting = Configuration::get('PS_USE_HTMLPURIFIER');
        Configuration::updateValue('PS_USE_HTMLPURIFIER', 1);
    }

    protected function tearDown(): void
    {
        Configuration::updateValue('PS_USE_HTMLPURIFIER', $this->initialPurifierSetting);

        parent::tearDown();
    }

    /**
     * The purifier is built once and cached, but $allow_style changes the element definition it is
     * built from. A strict call must not make every later permissive call strict.
     */
    public function testStyleIsAllowedEvenWhenAStrictCallCameFirst(): void
    {
        $strict = Tools::purifyHTML(self::MARKUP, null, false);

        // Control: this doubles as the check that purification is actually running.
        $this->assertStringNotContainsString('<style', $strict, 'Purification is not running, the rest of this test would be vacuous.');

        $permissive = Tools::purifyHTML(self::MARKUP, null, true);

        $this->assertStringContainsString('<style', $permissive, 'purifyHTML() ignored $allow_style because an earlier call had built the cached purifier without it.');
    }

    /**
     * The reverse leak: a permissive call must not relax purification for the callers that follow it.
     */
    public function testStyleIsStrippedEvenWhenAPermissiveCallCameFirst(): void
    {
        $permissive = Tools::purifyHTML(self::MARKUP, null, true);

        $this->assertStringContainsString('<style', $permissive, 'Purification kept nothing, the rest of this test would be vacuous.');

        $strict = Tools::purifyHTML(self::MARKUP, null, false);

        $this->assertStringNotContainsString('<style', $strict, 'purifyHTML() kept <style> for a caller that asked for strict purification, because an earlier permissive call had built the cached purifier.');
    }
}
