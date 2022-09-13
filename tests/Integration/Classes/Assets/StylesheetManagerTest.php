<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
