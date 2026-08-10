<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Api;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Entity\Translation;
use Tests\Integration\Utility\LoginTrait;

class TranslationControllerTest extends ApiTestCase
{
    use LoginTrait;

    private const NON_CORE_THEME = 'fakeThemeForTranslations';
    private const NON_CORE_THEME_DOMAIN = 'ShopThemeActions';
    private const NON_CORE_THEME_MESSAGE = 'Refresh';

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser(self::$client);
    }

    protected function tearDown(): void
    {
        self::getContainer()->get('doctrine')->getManager()
            ->createQuery('DELETE FROM ' . Translation::class . ' t WHERE t.theme = :theme')
            ->setParameter('theme', self::NON_CORE_THEME)
            ->execute()
        ;

        parent::tearDown();
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
                    'selected' => Theme::getDefaultTheme(),
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
                    'selected' => Theme::getDefaultTheme(),
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
     * An edited translation is stored under the theme it was edited for, and the database lookup is
     * scoped to that same theme. Serving the catalogue of a theme that is not a core one through the
     * core domain provider queries `theme IS NULL` instead, so the value that was just saved is never
     * read back.
     */
    public function testItReadsBackATranslationSavedForANonCoreTheme(): void
    {
        $edited = 'Translated for a non core theme';

        $this->assertOkResponseOnTranslationEdition([
            'locale' => 'en-US',
            'domain' => self::NON_CORE_THEME_DOMAIN,
            'default' => self::NON_CORE_THEME_MESSAGE,
            'edited' => $edited,
            'theme' => self::NON_CORE_THEME,
        ]);

        self::$client->request('GET', $this->router->generate('api_translation_domain_catalog', [
            'locale' => 'en-US',
            'domain' => self::NON_CORE_THEME_DOMAIN,
            'theme' => self::NON_CORE_THEME,
        ]));
        $catalogue = $this->assertResponseBodyValidJson(200)['data'];

        $this->assertSame(
            self::NON_CORE_THEME,
            $catalogue['info']['theme'],
            'A theme that is not a core one must be served by the theme provider.'
        );

        $messages = array_column($catalogue['data'], 'user', 'default');
        $this->assertArrayHasKey(self::NON_CORE_THEME_MESSAGE, $messages);
        $this->assertSame($edited, $messages[self::NON_CORE_THEME_MESSAGE]);
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
                    'theme' => Theme::getDefaultTheme(),
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
                    'theme' => Theme::getDefaultTheme(),
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
                'theme' => Theme::getDefaultTheme(),
            ],
            [
                'default' => 'AdminActions',
                'edited' => 'boo',
                'theme' => Theme::getDefaultTheme(),
            ],
            [
                'locale' => 'en-US',
            ],
            [
                'locale' => 'en-BOUH',
                'domain' => 'AdminActions',
                'default' => 'First message',
                'edited' => 'First translation',
                'theme' => Theme::getDefaultTheme(),
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
                'theme' => Theme::getDefaultTheme(),
            ],
            [
                'locale' => 'en-US',
            ],
            [
                'locale' => 'en-BOUH',
                'domain' => 'AdminActions',
                'default' => 'First message',
                'edited' => 'First translation',
                'theme' => Theme::getDefaultTheme(),
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
