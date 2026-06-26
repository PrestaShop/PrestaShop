<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use Configuration;
use FrontController;
use JavascriptManager;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use StylesheetManager;
use Tools;

/**
 * When a media server (CDN) is configured, remote assets registered by modules must keep their
 * absolute URL instead of being rewritten to the media server.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/36939
 * @see https://github.com/PrestaShop/PrestaShop/issues/36644
 */
class FrontControllerMediaServerTest extends TestCase
{
    private const REMOTE_URL = 'https://assets.example.com/sdk/ps_checkout-fo-sdk.js';

    /** @var array<string, mixed> */
    private array $originalConfig = [];

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['PS_JS_THEME_CACHE', 'PS_CSS_THEME_CACHE'] as $key) {
            $this->originalConfig[$key] = Configuration::get($key);
        }
        Configuration::updateValue('PS_JS_THEME_CACHE', 0);
        Configuration::updateValue('PS_CSS_THEME_CACHE', 0);
        // Tools::hasMediaServer() reads the _MEDIA_SERVER_*_ constants via a static cache; force the
        // cache to "1 media server configured" so the media-server branch is active in this test.
        $this->setMediaServerCount(1);
    }

    protected function tearDown(): void
    {
        foreach ($this->originalConfig as $key => $value) {
            Configuration::updateValue($key, $value);
        }
        $this->setMediaServerCount(null);
        parent::tearDown();
    }

    private function setMediaServerCount(?int $count): void
    {
        $property = (new ReflectionClass(Tools::class))->getProperty('_cache_nb_media_servers');
        $property->setAccessible(true);
        $property->setValue(null, $count);
    }

    public function testRemoteJavascriptKeepsItsUrlWhenMediaServerIsSet(): void
    {
        $manager = $this->createMock(JavascriptManager::class);
        // The remote URL must reach the asset manager untouched, with the 'remote' server flag.
        $manager->expects($this->once())
            ->method('register')
            ->with(
                'ps-checkout-sdk',
                self::REMOTE_URL,
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->anything(),
                'remote'
            );

        $controller = $this->createFrontController('javascriptManager', $manager);
        $controller->registerJavascript('ps-checkout-sdk', self::REMOTE_URL, ['server' => 'remote']);
    }

    public function testRemoteStylesheetKeepsItsUrlWhenMediaServerIsSet(): void
    {
        $manager = $this->createMock(StylesheetManager::class);
        $manager->expects($this->once())
            ->method('register')
            ->with(
                'ps-checkout-css',
                self::REMOTE_URL,
                $this->anything(),
                $this->anything(),
                $this->anything(),
                'remote'
            );

        $controller = $this->createFrontController('stylesheetManager', $manager);
        $controller->registerStylesheet('ps-checkout-css', self::REMOTE_URL, ['server' => 'remote']);
    }

    /**
     * Builds a usable FrontController without running its heavy constructor, with the given asset
     * manager injected into the matching protected property.
     */
    private function createFrontController(string $property, object $manager): FrontController
    {
        $controller = $this->getMockBuilder(FrontController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMockForAbstractClass();

        $reflection = new ReflectionClass(FrontController::class);
        $prop = $reflection->getProperty($property);
        $prop->setAccessible(true);
        $prop->setValue($controller, $manager);

        return $controller;
    }
}
