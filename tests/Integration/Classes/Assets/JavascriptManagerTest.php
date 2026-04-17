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
}
