<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\controller;

use Configuration;
use FrontControllerCore;
use JavascriptManager;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration as ConfigurationAdapter;
use ReflectionProperty;
use StylesheetManager;
use Tools;

/**
 * A media server must not rewrite assets a module registered with 'server' => 'remote':
 * their path is already an absolute URL, so it cannot be resolved on the local filesystem
 * and the asset was silently dropped.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/34423
 */
class FrontControllerRemoteAssetTest extends TestCase
{
    private const REMOTE_JS = 'https://cdn.example.com/library.js';
    private const REMOTE_CSS = 'https://cdn.example.com/library.css';

    /** @var array<string, mixed> */
    private array $backedUpConfiguration = [];

    private ?int $backedUpMediaServerCache = null;

    protected function setUp(): void
    {
        // The media server rewrite only runs when the theme cache is off
        foreach (['PS_JS_THEME_CACHE', 'PS_CSS_THEME_CACHE'] as $key) {
            $this->backedUpConfiguration[$key] = Configuration::get($key);
            Configuration::updateValue($key, 0);
        }

        // Pretend one media server is configured, without touching the _MEDIA_SERVER_*_ constants
        $this->backedUpMediaServerCache = $this->mediaServerCache()->getValue();
        $this->mediaServerCache()->setValue(null, 1);
    }

    protected function tearDown(): void
    {
        foreach ($this->backedUpConfiguration as $key => $value) {
            Configuration::updateValue($key, $value);
        }
        $this->mediaServerCache()->setValue(null, $this->backedUpMediaServerCache);
    }

    public function testItKeepsTheUrlOfARemotelyHostedJavascript(): void
    {
        $controller = new TestAssetFrontController();
        $controller->registerJavascript('remote-js', self::REMOTE_JS, ['server' => 'remote', 'position' => 'bottom']);

        $asset = $this->findAsset($controller->getJavascriptList(), 'remote-js');

        $this->assertNotNull($asset, 'the remotely hosted script was dropped');
        $this->assertSame(self::REMOTE_JS, $asset['uri']);
        $this->assertSame('remote', $asset['server']);
    }

    public function testItKeepsTheUrlOfARemotelyHostedStylesheet(): void
    {
        $controller = new TestAssetFrontController();
        $controller->registerStylesheet('remote-css', self::REMOTE_CSS, ['server' => 'remote']);

        $asset = $this->findAsset($controller->getStylesheetList(), 'remote-css');

        $this->assertNotNull($asset, 'the remotely hosted stylesheet was dropped');
        $this->assertSame(self::REMOTE_CSS, $asset['uri']);
        $this->assertSame('remote', $asset['server']);
    }

    public function testItStillSendsLocalAssetsToTheMediaServer(): void
    {
        $controller = new TestAssetFrontController();
        $controller->registerJavascript('local-js', '/js/jquery/jquery-3.7.1.min.js', ['position' => 'bottom']);

        $asset = $this->findAsset($controller->getJavascriptList(), 'local-js');

        $this->assertNotNull($asset, 'the local script was dropped');
        $this->assertSame('remote', $asset['server'], 'a local asset must still be handed to the media server');
    }

    /**
     * Stylesheets are listed as [type][id] and scripts as [position][type][id], so walk the
     * nesting instead of assuming a depth.
     *
     * @param array<string, mixed> $list
     *
     * @return array<string, mixed>|null
     */
    private function findAsset(array $list, string $id): ?array
    {
        foreach ($list as $key => $value) {
            if (!is_array($value)) {
                continue;
            }
            if ($key === $id && isset($value['id']) && $value['id'] === $id) {
                return $value;
            }
            $found = $this->findAsset($value, $id);
            if ($found !== null) {
                return $found;
            }
        }

        return null;
    }

    private function mediaServerCache(): ReflectionProperty
    {
        $property = new ReflectionProperty(Tools::class, '_cache_nb_media_servers');
        $property->setAccessible(true);

        return $property;
    }
}

class TestAssetFrontController extends FrontControllerCore
{
    public function __construct()
    {
        // same directory list the real controller gives its managers
        $directories = [_PS_THEME_URI_, _PS_PARENT_THEME_URI_, __PS_BASE_URI__];
        $configuration = new ConfigurationAdapter();
        $this->stylesheetManager = new StylesheetManager($directories, $configuration);
        $this->javascriptManager = new JavascriptManager($directories, $configuration);
    }

    public function getJavascriptList(): array
    {
        return $this->javascriptManager->getList();
    }

    public function getStylesheetList(): array
    {
        return $this->stylesheetManager->getList();
    }
}
