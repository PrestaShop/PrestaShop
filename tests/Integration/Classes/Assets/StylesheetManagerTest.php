<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Assets;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use StylesheetManager;

class StylesheetManagerTest extends TestCase
{
    /**
     * @dataProvider providerIsAssets
     */
    public function testIsAssets(string $id, bool $toBeFound, $expectedPath): void
    {
        $testsPath = '/tests/Resources/assets_manager/';

        $stylesheetManager = new StylesheetManager(
            [$testsPath, 'css'],
            new Configuration()
        );

        $stylesheetManager->register('theme-ok-1', '/theme.css', 'all', 10, false);
        $stylesheetManager->register('theme-fail-1', '/themee.css', 'all', 10, false);
        $stylesheetManager->register('theme-ok-2', 'theme.css', 'all', 10, false);
        $stylesheetManager->register('theme-fail-2', 'themee.css', 'all', 10, false);
        $stylesheetManager->register('theme-ok-3', '/css/custom.css', 'all', 10, false);
        $stylesheetManager->register('theme-fail-3', '/css/customm.css', 'all', 10, false);
        $stylesheetManager->register('theme-ok-4', 'css/custom.css', 'all', 10, false);
        $stylesheetManager->register('theme-fail-4', 'css/customm.css', 'all', 10, false);

        $expectedAsset = false;
        foreach ($stylesheetManager->getList()['external'] as $asset) {
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
        yield ['theme-ok-1', true, 'theme.css'];
        yield ['theme-fail-1', false, false];
        yield ['theme-ok-2', true, 'theme.css'];
        yield ['theme-fail-2', false, false];
        yield ['theme-ok-3', true, 'css/custom.css'];
        yield ['theme-fail-3', false, false];
        yield ['theme-ok-4', true, 'css/custom.css'];
        yield ['theme-fail-4', false, false];
    }
}
