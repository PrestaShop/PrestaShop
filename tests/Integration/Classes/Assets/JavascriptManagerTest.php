<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Assets;

use JavascriptManager;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;

class JavascriptManagerTest extends TestCase
{
    /**
     * @dataProvider providerIsAssets
     */
    public function testIsAssets(string $id, bool $toBeFound, $expectedPath): void
    {
        $expectedAsset = false;
        $testsPath = '/tests/Resources/assets_manager/';

        $javascriptManager = new JavascriptManager(
            [$testsPath, 'css'],
            new Configuration()
        );
        $javascriptManager->register('corejs-ok-1', '/core.js', 'bottom', 10, false, '');
        $javascriptManager->register('corejs-fail-1', '/coree.js', 'bottom', 10, false, '');
        $javascriptManager->register('corejs-ok-2', 'core.js', 'bottom', 10, false, '');
        $javascriptManager->register('corejs-fail-2', 'coree.js', 'bottom', 10, false, '');
        $javascriptManager->register('corejs-ok-3', '/js/core.js', 'bottom', 10, false, '');
        $javascriptManager->register('corejs-fail-3', '/js/coree.js', 'bottom', 10, false, '');
        $javascriptManager->register('corejs-ok-4', 'js/core.js', 'bottom', 10, false, '');
        $javascriptManager->register('corejs-fail-4', 'js/coree.js', 'bottom', 10, false, '');

        foreach ($javascriptManager->getList()['bottom']['external'] as $asset) {
            if ($asset['id'] === $id) {
                $expectedAsset = $asset;
            }
        }

        $this->assertSame($toBeFound, $expectedAsset !== false);

        if ($toBeFound) {
            $this->assertSame($expectedAsset['path'], $testsPath . $expectedPath);
        }
    }

    public function providerIsAssets(): iterable
    {
        yield ['corejs-ok-1', true, 'core.js'];
        yield ['corejs-fail-1', false, false];
        yield ['corejs-ok-2', true, 'core.js'];
        yield ['corejs-fail-2', false, false];
        yield ['corejs-ok-3', true, 'js/core.js'];
        yield ['corejs-fail-3', false, false];
        yield ['corejs-ok-4', true, 'js/core.js'];
        yield ['corejs-fail-4', false, false];
    }

    public function testAutoCacheBustingForLocalAssets(): void
    {
        $testsPath = '/tests/Resources/assets_manager/';
        $configuration = new Configuration();

        $javascriptManager = new JavascriptManager(
            [$testsPath],
            $configuration
        );

        $javascriptManager->register('cache-bust-test', '/core.js', 'bottom', 10, false);

        $list = $javascriptManager->getList();
        $asset = $list['bottom']['external']['cache-bust-test'];

        // URI should contain a query string with the file modification timestamp
        $this->assertStringContainsString('?', $asset['uri']);

        // Extract the version from URI
        $uriParts = explode('?', $asset['uri']);
        $version = $uriParts[1] ?? '';

        // Version should be a valid timestamp (numeric)
        $this->assertMatchesRegularExpression('/^\d+$/', $version);

        // Version should match the file's modification time
        $physicalPath = $configuration->get('_PS_ROOT_DIR_') . $testsPath . 'core.js';
        $expectedVersion = (string) filemtime($physicalPath);
        $this->assertSame($expectedVersion, $version);
    }

    public function testManualVersionOverridesAutoCacheBusting(): void
    {
        $testsPath = '/tests/Resources/assets_manager/';

        $javascriptManager = new JavascriptManager(
            [$testsPath],
            new Configuration()
        );

        $manualVersion = 'v1.2.3';
        $javascriptManager->register('manual-version-test', '/core.js', 'bottom', 10, false, null, 'local', $manualVersion);

        $list = $javascriptManager->getList();
        $asset = $list['bottom']['external']['manual-version-test'];

        // URI should contain the manual version, not auto-generated timestamp
        $this->assertStringContainsString('?' . $manualVersion, $asset['uri']);
    }

    public function testRemoteAssetsDoNotGetAutoCacheBusting(): void
    {
        $javascriptManager = new JavascriptManager(
            [],
            new Configuration()
        );

        $remoteUrl = 'https://cdn.example.com/script.js';
        $javascriptManager->register('remote-test', $remoteUrl, 'bottom', 10, false, null, 'remote');

        $list = $javascriptManager->getList();
        $asset = $list['bottom']['external']['remote-test'];

        // Remote URI should not have a query string added
        $this->assertSame($remoteUrl, $asset['uri']);
    }
}
