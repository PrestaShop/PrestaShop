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
        // Reset modules folder that are removed with the FR language
        (new ResourceResetter())->resetTestModules();
    }

    /**
     * @dataProvider getProtectedEndpoints
     *
     * @param string $method
     * @param string $uri
     */
    public function testProtectedEndpoints(string $method, string $uri): void
    {
        $response = static::createClient()->request($method, $uri);
        self::assertResponseStatusCodeSame(401);

        $content = $response->getContent(false);
        $this->assertNotEmpty($content);
        $this->assertEquals('No Authorization header provided', $content);
    }

    public function getProtectedEndpoints(): iterable
    {
        // The endpoint doesn't require any scope but you still need to be logged (have a valid token)
        yield 'get endpoint' => [
            'GET',
            '/languages',
        ];
    }

    public function testGetLanguages(): void
    {
        $paginatedLanguages = $this->listLanguages();
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
                'isRtl' => false,
                'active' => true,
            ],
            [
                'langId' => static::$frenchLangId,
                'name' => 'fr-FR',
                'isoCode' => 'fr',
                'languageCode' => 'fr-FR',
                'locale' => 'fr-FR',
                'dateFormat' => 'd/m/Y',
                'dateTimeFormat' => 'd/m/Y H:i:s',
                'isRtl' => false,
                'active' => true,
            ],
        ], $paginatedLanguages['items']);
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
