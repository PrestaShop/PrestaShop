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
        $javascriptManager->register('corejs-ok-1', '/core.js', 'bottom', 10, false, false);
        $javascriptManager->register('corejs-fail-1', '/coree.js', 'bottom', 10, false, false);
        $javascriptManager->register('corejs-ok-2', 'core.js', 'bottom', 10, false, false);
        $javascriptManager->register('corejs-fail-2', 'coree.js', 'bottom', 10, false, false);
        $javascriptManager->register('corejs-ok-3', '/js/core.js', 'bottom', 10, false, false);
        $javascriptManager->register('corejs-fail-3', '/js/coree.js', 'bottom', 10, false, false);
        $javascriptManager->register('corejs-ok-4', 'js/core.js', 'bottom', 10, false, false);
        $javascriptManager->register('corejs-fail-4', 'js/coree.js', 'bottom', 10, false, false);

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
