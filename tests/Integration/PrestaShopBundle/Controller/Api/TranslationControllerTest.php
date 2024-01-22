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

namespace Tests\Integration\PrestaShopBundle\Controller\Api;

use Tests\Integration\Utility\LoginTrait;

class TranslationControllerTest extends ApiTestCase
{
    use LoginTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser(self::$client);
    }

    /**
     * @dataProvider getBadLocales
     *
     * @param array $params
     */
    public function testItShouldReturnBadResponseWhenRequestingInvalidLocales(array $params): void
    {
        $this->assertBadRequest('api_translation_domain_catalog', $params);
    }

    /**
     * @dataProvider getGoodLocales
     *
     * @param array $params
     */
    public function testItShouldReturnOkResponseWhenRequestingValidLocales(array $params): void
    {
        $this->assertOkRequest('api_translation_domain_catalog', $params);
    }

    /**
     * @return array<array<string|int, array<string, string>>>
     */
    public function getBadLocales(): array
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
     * @return array<array<int, array<string, string>>>
     */
    public function getGoodLocales(): array
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
     *
     * @param array $params
     */
    public function testItShouldReturnBadResponseWhenRequestingDomainCatalog(array $params): void
    {
        $this->assertBadRequest('api_translation_domains_tree', $params);
    }

    /**
     * @dataProvider getGoodDomainsCatalog
     *
     * @param array $params
     */
    public function testItShouldReturnOkResponseWhenRequestingDomainCatalog(array $params): void
    {
        $this->assertOkRequest('api_translation_domains_tree', $params);
    }

    /**
     * @return array<array<int, array<string, string>>>
     */
    public function getBadDomainsCatalog(): array
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
     * @return array<array<int, array<string, string>>>
     */
    public function getGoodDomainsCatalog(): array
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

    public function testItShouldReturnErrorResponseWhenRequestingTranslationsEdition(): void
    {
        $this->assertErrorResponseOnTranslationEdition();
    }

    public function testItShouldReturnErrorResponseWhenRequestingTranslationsEditionWithData(): void
    {
        $this->assertErrorResponseOnTranslationEditionWithData();
    }

    /**
     * @dataProvider getGoodEditTranslations
     */
    public function testItShouldReturnValidResponseWhenRequestingTranslationsEdition(array $params): void
    {
        $this->assertOkResponseOnTranslationEdition($params);
    }

    /**
     * @return array<array<int, array<string, string>>>
     */
    public function getGoodEditTranslations(): array
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

    public function testItShouldReturnErrorResponseWhenRequestingTranslationsReset(): void
    {
        $this->assertErrorResponseOnTranslationReset();
    }

    public function testItShouldReturnErrorResponseWhenRequestingTranslationsResetWithData(): void
    {
        $this->assertErrorResponseOnTranslationResetWithData();
    }

    /**
     * @dataProvider getGoodResetTranslations
     */
    public function testItShouldReturnValidResponseWhenRequestingTranslationsReset(array $params): void
    {
        $this->assertOkResponseOnTranslationReset($params);
    }

    public function getGoodResetTranslations(): array
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

    private function assertErrorResponseOnTranslationEdition(): void
    {
        $editTranslationRoute = $this->router->generate(
            'api_translation_value_edit',
            ['locale' => 'en-US', 'domain' => 'AdminActions']
        );

        self::$client->request('POST', $editTranslationRoute);
        $this->assertResponseBodyValidJson(400);
    }

    private function assertErrorResponseOnTranslationEditionWithData(): void
    {
        self::$client->disableReboot();

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

    private function assertErrorResponseOnTranslationReset(): void
    {
        $resetTranslationRoute = $this->router->generate(
            'api_translation_value_reset',
            ['locale' => 'en-US', 'domain' => 'AdminActions']
        );

        self::$client->request('POST', $resetTranslationRoute);
        $this->assertResponseBodyValidJson(400);
    }

    private function assertErrorResponseOnTranslationResetWithData(): void
    {
        self::$client->disableReboot();

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

    private function assertOkResponseOnTranslationEdition(array $params): void
    {
        $editTranslationRoute = $this->router->generate(
        'api_translation_value_edit',
            ['locale' => 'en-US', 'domain' => 'AdminActions']
        );

        $post = json_encode(['translations' => [$params]]);
        self::$client->request('POST', $editTranslationRoute, [], [], [], $post);
        $this->assertResponseBodyValidJson(200);
    }

    private function assertOkResponseOnTranslationReset(array $params): void
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
