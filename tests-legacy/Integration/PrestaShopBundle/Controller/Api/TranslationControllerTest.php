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

namespace LegacyTests\Integration\PrestaShopBundle\Controller\Api;

/**
 * @group api
 * @group translation
 */
class TranslationControllerTest extends ApiTestCase
{
    /**
     * @dataProvider getBadLocales
     * @test
     *
     * @param $params
     */
    public function itShouldReturnBadResponseWhenRequestingInvalidLocales($params)
    {
        $this->assertBadRequest('api_translation_domain_catalog', $params);
    }

    /**
     * @dataProvider getGoodLocales
     * @test
     *
     * @param $params
     */
    public function itShouldReturnOkResponseWhenRequestingValidLocales($params)
    {
        $this->assertOkRequest('api_translation_domain_catalog', $params);
    }

    /**
     * @return array
     */
    public function getBadLocales()
    {
        return [
            [
                'syntax error wanted' => ['locale' => 'fr_Fr', 'domain' => 'AdminGlobal'],
            ],
            [
                ['locale' => 'defaultt', 'domain' => 'AdminGlobal'],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getGoodLocales()
    {
        return [
            [
                ['locale' => 'en-US', 'domain' => 'AdminGlobal'],
            ],
            [
                ['locale' => 'en-US', 'domain' => 'AdminNavigationMenu'],
            ],
        ];
    }

    /**
     * @dataProvider getBadDomainsCatalog
     * @test
     *
     * @param $params
     */
    public function itShouldReturnBadResponseWhenRequestingDomainCatalog($params)
    {
        $this->assertBadRequest('api_translation_domains_tree', $params);
    }

    /**
     * @dataProvider getGoodDomainsCatalog
     * @test
     *
     * @param $params
     */
    public function itShouldReturnOkResponseWhenRequestingDomainCatalog($params)
    {
        $this->assertOkRequest('api_translation_domains_tree', $params);
    }

    /**
     * @return array
     */
    public function getBadDomainsCatalog()
    {
        return [
            [
                [
                    'lang' => 'en',
                    'type' => 'modules',
                    'selected' => 'ps_baanner', // syntax error wanted
                ],
            ],
            [
                [
                    'lang' => 'en',
                    'type' => 'frront', // syntax error wanted
                    'selected' => 'classic',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getGoodDomainsCatalog()
    {
        return [
            [
                [
                    'lang' => 'en',
                    'type' => 'modules',
                    'selected' => 'ps_banner',
                ],
            ],
            [
                [
                    'lang' => 'en',
                    'type' => 'front',
                    'selected' => 'classic',
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function itShouldReturnErrorResponseWhenRequestingTranslationsEdition()
    {
        $this->assertErrorResponseOnTranslationEdition();
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function itShouldReturnErrorResponseWhenRequestingTranslationsEditionWithData()
    {
        $this->assertErrorResponseOnTranslationEditionWithData();
    }

    /**
     * @dataProvider getGoodEditTranslations
     * @test
     */
    public function itShouldReturnValidResponseWhenRequestingTranslationsEdition($params)
    {
        $this->assertOkResponseOnTranslationEdition($params);
    }

    public function getGoodEditTranslations()
    {
        return [
            [
                [
                    'locale' => 'en-US',
                    'domain' => 'AdminActions',
                    'default' => 'First message',
                    'edited' => 'First translation',
                    'theme' => 'classic',
                ],
            ],
            [
                [
                    'locale' => 'en-US',
                    'domain' => 'AdminActions',
                    'default' => 'Second message',
                    'edited' => 'Second translation',
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function itShouldReturnErrorResponseWhenRequestingTranslationsReset()
    {
        $this->assertErrorResponseOnTranslationReset();
    }

    /**
     * @runInSeparateProcess
     */
    public function itShouldReturnErrorResponseWhenRequestingTranslationsResetWithData()
    {
        $this->assertErrorResponseOnTranslationResetWithData();
    }

    /**
     * @dataProvider getGoodResetTranslations
     * @test
     */
    public function itShouldReturnValidResponseWhenRequestingTranslationsReset($params)
    {
        $this->assertOkResponseOnTranslationReset($params);
    }

    public function getGoodResetTranslations()
    {
        return [
            [
                [
                    'locale' => 'en-US',
                    'domain' => 'AdminActions',
                    'default' => 'First message',
                    'theme' => 'classic',
                ],
            ],
            [
                [
                    'locale' => 'en-US',
                    'domain' => 'AdminActions',
                    'default' => 'Second message',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    private function assertErrorResponseOnTranslationEdition()
    {
        $editTranslationRoute = $this->router->generate(
            'api_translation_value_edit',
            ['locale' => 'en-US', 'domain' => 'AdminActions']
        );

        self::$client->request('POST', $editTranslationRoute);
        $this->assertResponseBodyValidJson(400);
    }

    /**
     * @return array
     */
    private function assertErrorResponseOnTranslationEditionWithData()
    {
        $editTranslationRoute = $this->router->generate(
            'api_translation_value_edit',
            ['locale' => 'en-US', 'domain' => 'AdminActions']
        );

        self::$client->request('POST', $editTranslationRoute, [], [], [], '{}');
        $this->assertResponseBodyValidJson(400);

        $fails = [
            [
                'locale' => 'en-US',
                'domain' => 'AdminActions',
                'defaultfoo' => 'foo',
                'edited' => 'boo',
                'theme' => 'classic',
            ],
            [
                'default' => 'AdminActions',
                'edited' => 'boo',
                'theme' => 'classic',
            ],
            [
                'locale' => 'en-US',
            ],
            [
                'locale' => 'en-BOUH',
                'domain' => 'AdminActions',
                'default' => 'First message',
                'edited' => 'First translation',
                'theme' => 'classic',
            ],
        ];

        foreach ($fails as $fail) {
            $post = json_encode(['translations' => [$fail]]);
            self::$client->request('POST', $editTranslationRoute, [], [], [], $post);
            $this->assertResponseBodyValidJson(400);
        }
    }

    private function assertErrorResponseOnTranslationReset()
    {
        $resetTranslationRoute = $this->router->generate(
            'api_translation_value_reset',
            ['locale' => 'en-US', 'domain' => 'AdminActions']
        );

        self::$client->request('POST', $resetTranslationRoute);
        $this->assertResponseBodyValidJson(400);
    }

    private function assertErrorResponseOnTranslationResetWithData()
    {
        $resetTranslationRoute = $this->router->generate(
            'api_translation_value_reset',
            ['locale' => 'en-US', 'domain' => 'AdminActions']
        );

        self::$client->request('POST', $resetTranslationRoute, [], [], [], '{}');
        $this->assertResponseBodyValidJson(400);

        $fails = [
            [
                'locale' => 'en-US',
                'domain' => 'AdminActions',
                'defaultfoo' => 'foo',
            ],
            [
                'default' => 'foo',
                'theme' => 'classic',
            ],
            [
                'locale' => 'en-US',
            ],
            [
                'locale' => 'en-BOUH',
                'domain' => 'AdminActions',
                'default' => 'First message',
                'edited' => 'First translation',
                'theme' => 'classic',
            ],
        ];

        foreach ($fails as $fail) {
            $post = json_encode(['translations' => [$fail]]);
            self::$client->request('POST', $resetTranslationRoute, [], [], [], $post);
            $this->assertResponseBodyValidJson(400);
        }
    }

    /**
     * @return array
     */
    private function assertOkResponseOnTranslationEdition($params)
    {
        $editTranslationRoute = $this->router->generate(
            'api_translation_value_edit',
            ['locale' => 'en-US', 'domain' => 'AdminActions']
        );

        $post = json_encode(['translations' => [$params]]);
        self::$client->request('POST', $editTranslationRoute, [], [], [], $post);
        $this->assertResponseBodyValidJson(200);
    }

    private function assertOkResponseOnTranslationReset($params)
    {
        $resetTranslationRoute = $this->router->generate(
            'api_translation_value_reset',
            ['locale' => 'en-US', 'domain' => 'AdminActions']
        );

        $post = json_encode(['translations' => [$params]]);
        self::$client->request('POST', $resetTranslationRoute, [], [], [], $post);
        $this->assertResponseBodyValidJson(200);
    }
}
