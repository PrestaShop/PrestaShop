<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
