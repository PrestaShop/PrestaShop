<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\ApiPlatform\EndPoint;

use AdminAPIKernel;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase as ApiPlatformTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Configuration;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\AddApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use RuntimeException;
use Shop;
use ShopGroup;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\Integration\Utility\LanguageTrait;
use Tests\Resources\Resetter\ApiClientResetter;

abstract class ApiTestCase extends ApiPlatformTestCase
{
    use LanguageTrait;

    protected const CLIENT_ID = 'test_client_id';
    protected const CLIENT_NAME = 'test_client_name';

    /**
     * Secret of the single shared API Client created through the legacy createApiClient() path.
     * Kept for backward compatibility with tests that call createApiClient() in their setUp and then
     * read static::$clientSecret / static::CLIENT_ID directly (e.g. ApiAccessTokenEndpointTest).
     */
    protected static ?string $clientSecret = null;

    /**
     * Cache of API Clients created through the multi-scope path, keyed numerically.
     * Each entry is ['client_name' => string, 'client_id' => string, 'secret' => string, 'scopes' => array].
     *
     * @var array<int, array{client_name: string, client_id: string, secret: string, scopes: array}>|null
     */
    protected static ?array $apiClients = null;

    /**
     * Cache of bearer tokens created through the multi-scope path, to avoid hitting /access_token repeatedly.
     *
     * @var array<int, array{token: string, scopes: array}>|null
     */
    protected static ?array $bearerTokens = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::updateConfiguration('PS_ADMIN_API_FORCE_DEBUG_SECURED', 0);
        ApiClientResetter::resetApiClient();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        ApiClientResetter::resetApiClient();
        self::updateConfiguration('PS_ADMIN_API_FORCE_DEBUG_SECURED', 1);
        self::$clientSecret = null;
        self::$apiClients = null;
        self::$bearerTokens = null;
    }

    /**
     * @dataProvider getProtectedEndpoints
     *
     * @param string $method
     * @param string $uri
     * @param string $contentType
     * @param bool $scopeNeeded
     */
    public function testProtectedEndpoints(string $method, string $uri, string $contentType = 'application/json', bool $scopeNeeded = true): void
    {
        $options['headers']['content-type'] = $contentType;
        // Check that endpoints are not accessible without a proper Bearer token
        $response = static::createClient([], $options)->request($method, $uri);
        self::assertResponseStatusCodeSame(401);

        $content = $response->getContent(false);
        $this->assertNotEmpty($content);
        $this->assertEquals('"No Authorization header provided"', $content);

        // Test same endpoint with a token but without scopes
        if ($scopeNeeded) {
            $emptyBearerToken = $this->getBearerToken();
            static::createClient([], $options)->request($method, $uri, ['auth_bearer' => $emptyBearerToken]);
            self::assertResponseStatusCodeSame(403);
        }
    }

    /**
     * You must provide a list of protected endpoints that will be automatically checked,
     * the test will check that the endpoints are not accessible when no token is specified
     * AND that they are not accessible when the no particular scope is specified.
     *
     * You should use yield return like this:
     *
     *  yield 'get endpoint' => [
     *      'GET',
     *      '/product/1',
     *  ];
     *
     * @return iterable
     */
    public function getProtectedEndpoints(): iterable
    {
        // Before we could return a EmptyIterator but now PHPUnit forces at least one element in the iterable
        yield 'infos endpoint' => [
            'GET',
            '/api-clients/infos',
            'application/json',
            // The endpoint is protected when you have no token, however it doesn't require any particular scope
            false,
        ];
    }

    /**
     * API endpoints are only available in the OAuth application so we force using the proper kernel here.
     *
     * @return string
     */
    protected static function getKernelClass(): string
    {
        return AdminAPIKernel::class;
    }

    protected static function bootKernel(array $options = []): KernelInterface
    {
        $bootKernel = parent::bootKernel($options);
        // We must define the global $kernel variable for legacy code to access the container (see SymfonyContainer::getInstance)
        global $kernel;
        $kernel = $bootKernel;

        return $bootKernel;
    }

    protected static function createClient(array $kernelOptions = [], array $defaultOptions = []): Client
    {
        if (!isset($defaultOptions['headers']['accept'])) {
            $defaultOptions['headers']['accept'] = ['application/json'];
        }

        if (!isset($defaultOptions['headers']['content-type'])) {
            $defaultOptions['headers']['content-type'] = ['application/json'];
        }

        return parent::createClient($kernelOptions, $defaultOptions);
    }

    /**
     * Get a bearer token usable for the requested scopes.
     *
     * Two compatible strategies coexist:
     *  - Legacy single-client path: when a test created the shared client via createApiClient() (so
     *    static::$clientSecret is set), the token is requested against that client using static::CLIENT_ID.
     *    This preserves the historical behaviour relied on by existing endpoint tests.
     *  - Multi-scope path: otherwise (no shared client created), a dedicated API Client owning the requested
     *    scopes is found in the cache or created on the fly, and tokens are cached per-scope to limit
     *    /access_token round-trips. Token caching is skipped when custom kernel/client options are passed.
     *
     * @param array $scopes List of scopes to request the token for
     * @param array $kernelOptions Custom kernel options (e.g. debug mode); forwarded to the /access_token request
     * @param array $clientOptions Custom client options; forwarded to the /access_token request
     */
    protected function getBearerToken(array $scopes = [], array $kernelOptions = [], array $clientOptions = []): string
    {
        // Legacy behaviour: a single shared API Client was explicitly created by the test, always use it.
        if (null !== self::$clientSecret) {
            $options = [
                'extra' => [
                    'parameters' => [
                        'client_id' => static::CLIENT_ID,
                        'client_secret' => static::$clientSecret,
                        'grant_type' => 'client_credentials',
                        'scope' => $scopes,
                    ],
                ],
                'headers' => [
                    'content-type' => 'application/x-www-form-urlencoded',
                ],
            ];
            $response = static::createClient($kernelOptions, $clientOptions)->request('POST', '/access_token', $options);

            return json_decode($response->getContent())->access_token;
        }

        if (null === static::$bearerTokens) {
            static::$bearerTokens = [];
        }

        // Custom kernel/client options (e.g. a specific debug mode, like DisabledEndpointsTest uses) change which
        // operations/routes the kernel exposes, so a token cached under different options must not be reused: doing
        // so would also skip the /access_token request that warms the route cache under those very options, leaving
        // a stale cache. We therefore only cache (and reuse) tokens for the default options.
        $useTokenCache = empty($kernelOptions) && empty($clientOptions);

        // If a token with these scopes already exists then we reuse it, it prevents requesting the /access_token API all the time
        if ($useTokenCache) {
            foreach (static::$bearerTokens as $bearerToken) {
                $containsAllScopes = count(array_intersect($bearerToken['scopes'], $scopes)) === count($scopes);
                if ($containsAllScopes) {
                    return $bearerToken['token'];
                }
            }
        }

        // No token was found that can use all the request scopes so we create a new one
        $apiClient = static::getApiClientWithScopes($scopes);
        $parameters = ['parameters' => [
            'client_id' => $apiClient['client_id'],
            'client_secret' => $apiClient['secret'],
            'grant_type' => 'client_credentials',
            'scope' => $scopes,
        ]];
        $options = [
            'extra' => $parameters,
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded',
            ],
        ];
        // Forward the kernel/client options so the token is requested against the same kernel configuration as the
        // subsequent calls (this also warms the route cache under those options before the actual request).
        $response = static::createClient($kernelOptions, $clientOptions)->request('POST', '/access_token', $options);
        self::assertResponseStatusCodeSame(200);

        $bearerToken = json_decode($response->getContent())->access_token;

        // Cache the bearer token with associated scopes
        if ($useTokenCache) {
            static::$bearerTokens[] = [
                'token' => $bearerToken,
                'scopes' => $scopes,
            ];
        }

        return $bearerToken;
    }

    /**
     * @param string $httpMethod HTTP method to use (GET, POST, PATCH, PUT, DELETE)
     * @param string $endPointUrl HTTP url for the requested endpoint
     * @param array|null $data Flatten array data that will be sent as JSON (null if no JSON is sent)
     * @param array $scopes List of scopes to use for this request, a Bearer token will be requested based on the list and use for the request
     * @param int|null $expectedHttpCode Expected HTTP code, by default it will be inferred based on the HTTP method used but you can specify it (especially when you want to assert requests that will return error codes)
     * @param array|null $requestOptions Specify custom options for the request (useful to change the content-type, upload files, ...))
     *
     * @return array|string|null JSON response is returned as an array, unless it only contains one string message, null is returned for NoContent responses
     */
    protected function requestApi(string $httpMethod, string $endPointUrl, ?array $data = null, array $scopes = [], ?int $expectedHttpCode = null, ?array $requestOptions = null): array|string|null
    {
        $options = [];
        if (!empty($scopes)) {
            $bearerToken = $this->getBearerToken($scopes);
            $options['auth_bearer'] = $bearerToken;
        }

        // JSON option is only set when the JSON is not empty
        if (!empty($data)) {
            $options['json'] = $data;
        }

        // Merge additional options if present
        if (null !== $requestOptions) {
            $options = array_merge($options, $requestOptions);
        }

        $response = static::createClient()->request($httpMethod, $endPointUrl, $options);

        // Unless you mean to test a specific code (for invalid requests mostly) the expected code can be deduced from the
        // HTTP method by convention
        if (null === $expectedHttpCode) {
            $expectedHttpCode = match ($httpMethod) {
                Request::METHOD_POST => Response::HTTP_CREATED,
                Request::METHOD_DELETE => Response::HTTP_NO_CONTENT,
                default => Response::HTTP_OK,
            };
        }

        self::assertResponseStatusCodeSame($expectedHttpCode);
        $content = $response->getContent(false);

        // Some endpoint returns no content (204 code should be used in this case)
        if (empty($content)) {
            $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

            return null;
        }

        $decodedResponse = json_decode($content, true);
        $this->assertNotFalse($decodedResponse);

        return $decodedResponse;
    }

    /**
     * Performs a GET request to get a single item.
     */
    protected function getItem(string $endPointUrl, array $scopes = [], ?int $expectedHttpCode = null, ?array $requestOptions = null): array|string|null
    {
        return $this->requestApi(Request::METHOD_GET, $endPointUrl, null, $scopes, $expectedHttpCode, $requestOptions);
    }

    /**
     * Performs a POST request to create an item
     */
    protected function createItem(string $endPointUrl, ?array $data, array $scopes = [], ?int $expectedHttpCode = null, ?array $requestOptions = null): array|string|null
    {
        return $this->requestApi(Request::METHOD_POST, $endPointUrl, $data, $scopes, $expectedHttpCode, $requestOptions);
    }

    /**
     * Performs a PATCH request to partially update an item
     */
    protected function partialUpdateItem(string $endPointUrl, ?array $data, array $scopes = [], ?int $expectedHttpCode = null, ?array $requestOptions = null): array|string|null
    {
        return $this->requestApi(Request::METHOD_PATCH, $endPointUrl, $data, $scopes, $expectedHttpCode, $requestOptions);
    }

    /**
     * Performs a PUT request to completely update an item
     */
    protected function updateItem(string $endPointUrl, ?array $data, array $scopes = [], ?int $expectedHttpCode = null, ?array $requestOptions = null): array|string|null
    {
        return $this->requestApi(Request::METHOD_PUT, $endPointUrl, $data, $scopes, $expectedHttpCode, $requestOptions);
    }

    /**
     * Performs a DELETE request to delete an item
     */
    protected function deleteItem(string $endPointUrl, array $scopes = [], ?int $expectedHttpCode = null, ?array $requestOptions = null): array|string|null
    {
        return $this->requestApi(Request::METHOD_DELETE, $endPointUrl, null, $scopes, $expectedHttpCode, $requestOptions);
    }

    /**
     * Performs a DELETE request to delete multiple items, which requires passing some data
     */
    protected function bulkDeleteItems(string $endPointUrl, ?array $data, array $scopes = [], ?int $expectedHttpCode = null, ?array $requestOptions = null): array|string|null
    {
        return $this->requestApi(Request::METHOD_DELETE, $endPointUrl, $data, $scopes, $expectedHttpCode, $requestOptions);
    }

    protected function bulkCommandItemsWithExpectedErrors(string $httpMethod, string $endPointUrl, ?array $data, ?array $expectedErrors, array $scopes = [], ?array $requestOptions = null): array|string|null
    {
        // In 9.1, we added a new normalizer to handle bulk command exceptions and return a 207 code with details about each error.
        // If the normalizer is not present, it means we are on an older version and the API will return a 422 code with a generic error message,
        // so we adapt our assertions based on the normalizer presence.
        $newNormalizer = class_exists("PrestaShopBundle\ApiPlatform\Normalizer\BulkCommandExceptionNormalizer");
        $expectedHttpCode = $newNormalizer ? Response::HTTP_MULTI_STATUS : Response::HTTP_UNPROCESSABLE_ENTITY;

        $responseData = $this->requestApi($httpMethod, $endPointUrl, $data, $scopes, $expectedHttpCode, $requestOptions);

        if ($newNormalizer && null !== $expectedErrors) {
            $this->assertArrayHasKey('errors', $responseData);
            $this->assertIsArray($responseData['errors']);
            $this->assertEquals($expectedErrors, $responseData['errors']);
        }

        return $responseData;
    }

    /**
     * Performs a GET request to list some items, returned data is paginated
     */
    protected function listItems(string $listUrl, array $scopes = [], array $filters = []): array
    {
        $bearerToken = $this->getBearerToken($scopes);
        $response = static::createClient()->request('GET', $listUrl, [
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

    /**
     * Performs a GET request to list some items, but simply return the count
     */
    protected function countItems(string $listUrl, array $scopes = [], array $filters = []): int
    {
        $list = $this->listItems($listUrl, $scopes, $filters);

        return $list['totalItems'];
    }

    protected function prepareUploadedFile(string $assetFilePath): UploadedFile
    {
        // Uploaded file must be a temporary copy because the file will be moved by the API
        $tmpUploadedImagePath = rtrim(sys_get_temp_dir(), '/') . '/' . basename($assetFilePath);
        copy($assetFilePath, $tmpUploadedImagePath);

        return new UploadedFile($tmpUploadedImagePath, basename($assetFilePath));
    }

    protected function assertValidationErrors(array $expectedErrors, array $responseErrors): void
    {
        foreach ($responseErrors as $errorDetail) {
            $this->assertArrayHasKey('propertyPath', $errorDetail);
            $this->assertArrayHasKey('message', $errorDetail);
            $this->assertArrayHasKey('code', $errorDetail);

            $errorFound = false;
            foreach ($expectedErrors as $expectedError) {
                if (
                    (empty($expectedError['message']) || $expectedError['message'] === $errorDetail['message'])
                    && (empty($expectedError['propertyPath']) || $expectedError['propertyPath'] === $errorDetail['propertyPath'])
                ) {
                    $errorFound = true;
                    break;
                }
            }

            $this->assertTrue($errorFound, 'Found error that was not expected: ' . var_export($errorDetail, true));
        }

        foreach ($expectedErrors as $expectedError) {
            $errorFound = false;
            foreach ($responseErrors as $errorDetail) {
                if (
                    (empty($expectedError['message']) || $expectedError['message'] === $errorDetail['message'])
                    && (empty($expectedError['propertyPath']) || $expectedError['propertyPath'] === $errorDetail['propertyPath'])
                ) {
                    $errorFound = true;
                    break;
                }
            }

            $this->assertTrue($errorFound, 'Could not find expected error: ' . var_export($expectedError, true));
        }
    }

    /**
     * Creates the single shared API Client (legacy path).
     *
     * This keeps the historical contract used by existing endpoint tests: it sets static::$clientSecret and
     * registers the client under the fixed static::CLIENT_ID / static::CLIENT_NAME. Tests calling this method
     * in their setUp continue to use the legacy single-client token strategy in getBearerToken().
     */
    protected static function createApiClient(array $scopes = [], int $lifetime = 10000): void
    {
        $command = new AddApiClientCommand(
            static::CLIENT_NAME,
            static::CLIENT_ID,
            true,
            '',
            $lifetime,
            $scopes
        );

        $container = static::createClient()->getContainer();
        $commandBus = $container->get('prestashop.core.command_bus');
        $createdApiClient = $commandBus->handle($command);

        self::$clientSecret = $createdApiClient->getSecret();
    }

    /**
     * Creates a dedicated API Client owning the given scopes (multi-scope path).
     *
     * Unlike createApiClient(), each client gets a unique name/id derived from its scopes so several clients
     * with different scope sets can coexist within the same test class. The created client is cached and the
     * full descriptor (including its secret) is returned.
     *
     * @return array{client_name: string, client_id: string, secret: string, scopes: array}
     */
    protected static function createApiClientWithScopes(array $scopes = [], int $lifetime = 10000): array
    {
        if (null === self::$apiClients) {
            self::$apiClients = [];
        }

        $apiClient = [
            'client_name' => md5(implode(',', $scopes)),
            'client_id' => md5(implode(',', $scopes)),
            'scopes' => $scopes,
        ];

        $command = new AddApiClientCommand(
            $apiClient['client_name'],
            $apiClient['client_id'],
            true,
            '',
            $lifetime,
            $scopes
        );

        $container = static::createClient()->getContainer();
        $commandBus = $container->get('prestashop.core.command_bus');
        $createdApiClient = $commandBus->handle($command);

        $apiClient['secret'] = $createdApiClient->getSecret();
        self::$apiClients[] = $apiClient;

        return $apiClient;
    }

    /**
     * Get an API client that has permissions over the provided scopes, create it if it doesn't exist.
     *
     * @return array{client_name: string, client_id: string, secret: string, scopes: array}
     */
    protected static function getApiClientWithScopes(array $scopes = []): array
    {
        if (null === self::$apiClients) {
            self::$apiClients = [];
        }

        foreach (static::$apiClients as $apiClient) {
            $containsAllScopes = count(array_intersect($apiClient['scopes'], $scopes)) === count($scopes);
            if ($containsAllScopes) {
                return $apiClient;
            }
        }

        // API Client with all the scopes was not found so we create a new one with the required scopes
        return self::createApiClientWithScopes($scopes);
    }

    protected static function addShopGroup(string $groupName, ?string $color = null): int
    {
        $shopGroup = new ShopGroup();
        $shopGroup->name = $groupName;
        $shopGroup->active = true;

        if ($color !== null) {
            $shopGroup->color = $color;
        }

        if (!$shopGroup->add()) {
            throw new RuntimeException('Could not create shop group');
        }

        return (int) $shopGroup->id;
    }

    protected static function addShop(string $shopName, int $shopGroupId, ?string $color = null): int
    {
        $shop = new Shop();
        $shop->active = true;
        $shop->id_shop_group = $shopGroupId;
        // 2 : ID Category for "Home" in database
        $shop->id_category = 2;
        $shop->theme_name = _THEME_NAME_;
        $shop->name = $shopName;
        if ($color !== null) {
            $shop->color = $color;
        }

        if (!$shop->add()) {
            throw new RuntimeException('Could not create shop');
        }
        $shop->setTheme();
        Shop::resetContext();
        Shop::resetStaticCache();

        return (int) $shop->id;
    }

    protected static function updateConfiguration(string $configurationKey, $value, ?ShopConstraint $shopConstraint = null): void
    {
        self::getContainer()->get(ShopConfigurationInterface::class)->set($configurationKey, $value, $shopConstraint ?: ShopConstraint::allShops());
        Configuration::resetStaticCache();
    }
}
