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

namespace Tests\Unit\PrestaShopBundle\Translation\Loader;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Loader\LegacyTranslationKey;

class LegacyTranslationKeyTest extends TestCase
{
    /**
     * @param string $key
     * @param string $expectedModule
     * @param string $expectedTheme
     * @param string $expectedSource
     * @param string $expectedHash
     *
     * @dataProvider provideTestCases
     */
    public function testItParsesKeys($key, $expectedModule, $expectedTheme, $expectedSource, $expectedHash)
    {
        $parsed = LegacyTranslationKey::buildFromString($key);

        $this->assertSame($expectedModule, $parsed->getModule());
        $this->assertSame($expectedTheme, $parsed->getTheme());
        $this->assertSame($expectedSource, $parsed->getSource());
        $this->assertSame($expectedHash, $parsed->getHash());
    }

    public function provideTestCases()
    {
        return [
            [
                '<{psgdpr}prestashop>psgdpr_5966265f35dd87febf4d59029bc9ef66',
                'psgdpr',
                'prestashop',
                'psgdpr',
                '5966265f35dd87febf4d59029bc9ef66',
            ],
            [
                '<{psgdpr}prestashop>htmltemplatepsgdprmodule_9ad5a301cfed1c7f825506bf57205ab6',
                'psgdpr',
                'prestashop',
                'htmltemplatepsgdprmodule',
                '9ad5a301cfed1c7f825506bf57205ab6',
            ],
            [
                '<{psgdpr}prestashop>personaldata.connections-tab_33e29c1d042c0923008f78b46af94984',
                'psgdpr',
                'prestashop',
                'personaldata.connections-tab',
                '33e29c1d042c0923008f78b46af94984',
            ],
            [
                '<{somemodule}sometheme>somesource_33e29c1d042c0923008f78b46af94984',
                'somemodule',
                'sometheme',
                'somesource',
                '33e29c1d042c0923008f78b46af94984',
            ],
        ];
    }
}
