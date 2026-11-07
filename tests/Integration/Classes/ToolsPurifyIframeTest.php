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

class ToolsPurifyIframeTest extends TestCase
{
    /**
     * The markup the YouTube share dialog hands a merchant.
     */
    private const YOUTUBE_EMBED = '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ"'
        . ' title="YouTube video player" frameborder="0"'
        . ' allow="accelerometer; autoplay; encrypted-media; picture-in-picture; web-share"'
        . ' allowfullscreen></iframe>';

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

    public function testTheFullscreenControlOfAnEmbedSurvivesPurification(): void
    {
        $purified = Tools::purifyHTML(self::YOUTUBE_EMBED);

        // Control: if the frame itself were dropped, the assertion below would be about nothing.
        $this->assertStringContainsString('<iframe', $purified, 'The frame was dropped, so this test would say nothing about its attributes.');

        $this->assertStringContainsString('allowfullscreen', $purified, 'purifyHTML() dropped allowfullscreen, so the embed cannot go fullscreen.');
    }

    /**
     * `allow` delegates camera, microphone, geolocation and payment to the embedded origin, so it is
     * deliberately not declared. This pins that decision.
     */
    public function testThePermissionsPolicyOfAnEmbedIsStillRemoved(): void
    {
        $purified = Tools::purifyHTML(self::YOUTUBE_EMBED);

        $this->assertStringNotContainsString('allow=', $purified, 'purifyHTML() kept the allow attribute, which hands powerful permissions to the embedded origin.');
    }

    public function testTheAttributesTheFrameAlreadyKeptAreUnaffected(): void
    {
        $purified = Tools::purifyHTML(self::YOUTUBE_EMBED);

        $this->assertStringContainsString('src="https://www.youtube.com/embed/dQw4w9WgXcQ"', $purified);
        $this->assertStringContainsString('width="560"', $purified);
        $this->assertStringContainsString('title="YouTube video player"', $purified);
    }
}
