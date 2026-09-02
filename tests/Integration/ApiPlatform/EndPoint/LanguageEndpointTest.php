<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\ApiPlatform\EndPoint;

use Tests\Resources\Resetter\LanguageResetter;
use Tests\Resources\ResourceResetter;

class LanguageEndpointTest extends ApiTestCase
{
    protected static int $frenchLangId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        (new ResourceResetter())->backupTestModules();
        LanguageResetter::resetLanguages();
        self::$frenchLangId = self::addLanguageByLocale('fr-FR');
        self::createApiClient();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        LanguageResetter::resetLanguages();
    }

    public function getProtectedEndpoints(): iterable
    {
        // The endpoint doesn't require any scope but you still need to be logged (have a valid token)
        yield 'get endpoint' => [
            'GET',
            '/languages',
            'application/json',
            // The endpoint is protected when you have no token, however it doesn't require any particular scope
            false,
        ];
    }

    public function testGetLanguages(): void
    {
        $paginatedLanguages = $this->listLanguages();
        $items = $paginatedLanguages['items'];
        // We delete the end of the flag path because it is a timestamp and could cause random fails in tests.
        foreach ($items as $key => $item) {
            $items[$key]['flag'] = substr($items[$key]['flag'], 0, strpos($items[$key]['flag'], '?'));
        }
        $this->assertEquals(2, $paginatedLanguages['totalItems']);
        $this->assertEquals([
            [
                'langId' => 1,
                'name' => 'English (English)',
                'isoCode' => 'en',
                'languageCode' => 'en-us',
                'locale' => 'en-US',
                'dateFormat' => 'm/d/Y',
                'dateTimeFormat' => 'm/d/Y H:i:s',
                'rtl' => false,
                'enabled' => true,
                'flag' => '/img/tmp/lang_mini_1_1.jpg',
            ],
            [
                'langId' => static::$frenchLangId,
                'name' => 'fr-FR',
                'isoCode' => 'fr',
                'languageCode' => 'fr-FR',
                'locale' => 'fr-FR',
                'dateFormat' => 'd/m/Y',
                'dateTimeFormat' => 'd/m/Y H:i:s',
                'rtl' => false,
                'enabled' => true,
                'flag' => '/img/tmp/lang_mini_' . static::$frenchLangId . '_1.jpg',
            ],
        ], $items);
    }

    public function testFilterLanguages(): void
    {
        $paginatedLanguages = $this->listLanguages(['langId' => 1]);
        $this->assertEquals(1, $paginatedLanguages['totalItems']);
        $this->assertEquals('en-US', $paginatedLanguages['items'][0]['locale']);

        $paginatedLanguages = $this->listLanguages(['langId' => static::$frenchLangId]);
        $this->assertEquals(1, $paginatedLanguages['totalItems']);
        $this->assertEquals('fr-FR', $paginatedLanguages['items'][0]['locale']);

        $paginatedLanguages = $this->listLanguages(['name' => 'eng']);
        $this->assertEquals(1, $paginatedLanguages['totalItems']);
        $this->assertEquals('en-US', $paginatedLanguages['items'][0]['locale']);

        $paginatedLanguages = $this->listLanguages(['name' => 'fr']);
        $this->assertEquals(1, $paginatedLanguages['totalItems']);
        $this->assertEquals('fr-FR', $paginatedLanguages['items'][0]['locale']);
    }

    private function listLanguages(array $filters = []): array
    {
        $bearerToken = $this->getBearerToken();
        $response = static::createClient()->request('GET', '/languages', [
            'auth_bearer' => $bearerToken,
            'extra' => [
                'parameters' => [
                    'filters' => $filters,
                ],
            ],
        ]);
        self::assertResponseStatusCodeSame(200);

        $decodedResponse = json_decode($response->getContent(), true);
        $this->assertNotFalse($decodedResponse);
        $this->assertArrayHasKey('totalItems', $decodedResponse);
        $this->assertArrayHasKey('sortOrder', $decodedResponse);
        $this->assertArrayHasKey('limit', $decodedResponse);
        $this->assertArrayHasKey('filters', $decodedResponse);
        $this->assertArrayHasKey('items', $decodedResponse);

        return $decodedResponse;
    }
}
