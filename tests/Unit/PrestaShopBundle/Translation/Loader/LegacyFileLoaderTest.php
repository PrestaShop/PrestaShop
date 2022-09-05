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
use PrestaShopBundle\Translation\Exception\InvalidLegacyTranslationKeyException;
use PrestaShopBundle\Translation\Loader\LegacyFileLoader;
use PrestaShopBundle\Translation\Loader\LegacyFileReader;
use Symfony\Component\Translation\MessageCatalogue;

class LegacyFileLoaderTest extends TestCase
{
    public function testItInterpretsLegacyTranslationFileData()
    {
        $path = '/some/path/to/module/translations/';
        $locale = 'fr-FR';
        $translations = [
            '<{psgdpr}prestashop>psgdpr_5966265f35dd87febf4d59029bc9ef66' => 'RGPD Officiel ',
            '<{psgdpr}prestashop>htmltemplatepsgdprmodule_9ad5a301cfed1c7f825506bf57205ab6' => 'DONNÉES PERSONNELLES',
            '<{psgdpr}prestashop>htmltemplatepsgdprmodule_ce114e4501d2f4e2dcea3e17b546f339' => 'This is a test',
            '<{psgdpr}prestashop>personaldata.connections-tab_33e29c1d042c0923008f78b46af94984' => 'Demande d\'origine',
            '<{somemodule}sometheme>somesource_57f32d7d0e6672cc2b60bc7a49f91453' => 'Page consultée',
        ];

        $expected = [
            'ModulesPsgdprPsgdpr' => [
                '5966265f35dd87febf4d59029bc9ef66' => 'RGPD Officiel ',
            ],
            'ModulesPsgdprHtmltemplatepsgdprmodule' => [
                '9ad5a301cfed1c7f825506bf57205ab6' => 'DONNÉES PERSONNELLES',
                'ce114e4501d2f4e2dcea3e17b546f339' => 'This is a test',
            ],
            'ModulesPsgdprPersonaldata.connections-tab' => [
                '33e29c1d042c0923008f78b46af94984' => 'Demande d\'origine',
            ],
            'ModulesSomemoduleSomesource' => [
                '57f32d7d0e6672cc2b60bc7a49f91453' => 'Page consultée',
            ],
        ];

        $loader = new LegacyFileLoader($this->getMockReader($path, $locale, $translations));

        $catalogue = $loader->load($path, $locale);

        $this->verifyCatalogue($catalogue, $expected);
    }

    public function testItThrowsAnExceptionIfKeyIsInvalid()
    {
        $this->expectException(InvalidLegacyTranslationKeyException::class);

        $path = '/some/path/to/module/translations/';
        $locale = 'fr-FR';
        $translations = [
            '<{wrong' => "Won't work",
        ];

        $loader = new LegacyFileLoader($this->getMockReader($path, $locale, $translations));
        $loader->load($path, $locale);
    }

    private function getMockReader(string $path, string $locale, array $translations): LegacyFileReader
    {
        $mock = $this->getMockBuilder(LegacyFileReader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->method('load')->with($path, $locale)->willReturn($translations);

        return $mock;
    }

    /**
     * @param MessageCatalogue $messageCatalogue
     * @param array[] $expected
     */
    private function verifyCatalogue(MessageCatalogue $messageCatalogue, $expected)
    {
        $domains = $messageCatalogue->getDomains();

        foreach ($expected as $expectedDomain => $expectedStrings) {
            // the domain should be defined
            $this->assertContains(
                $expectedDomain,
                $domains,
                sprintf('Domain "%s" is not defined in %s', $expectedDomain, print_r($domains, true))
            );

            // all strings should be defined in the appropriate domain
            foreach ($expectedStrings as $key => $string) {
                $this->assertTrue(
                    $messageCatalogue->defines($key, $expectedDomain),
                    sprintf('"%s" not found in %s', $string, $expectedDomain)
                );

                $this->assertSame(
                    $messageCatalogue->get($key, $expectedDomain),
                    $string,
                    sprintf(
                        'The translation result for "%s" was expected to be "%s" but was "%s',
                        $key,
                        $string,
                        $messageCatalogue->get($key, $expectedDomain)
                    )
                );
            }
        }
    }
}
