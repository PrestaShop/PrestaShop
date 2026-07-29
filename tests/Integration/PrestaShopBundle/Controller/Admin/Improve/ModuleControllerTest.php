<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Improve;

use Context;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Feature\TokenInUrls;
use PrestaShop\PrestaShop\Core\Module\ModuleCollection;
use PrestaShop\PrestaShop\Core\Module\ModuleInterface;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Translation\Translator;
use Tests\Integration\Utility\LoginTrait;

class ModuleControllerTest extends WebTestCase
{
    use LoginTrait;

    /**
     * @var KernelBrowser
     */
    protected $client;
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var Translator
     */
    protected $translator;
    /**
     * @var LegacyContext
     */
    protected $context;

    /**
     * Records the module name received by ModuleRepository::getModule() during a request.
     *
     * @var string|null
     */
    protected $capturedConfiguredModuleName;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
        $this->loginUser($this->client);
        $this->router = self::$kernel->getContainer()->get('router');
        $this->context = self::$kernel->getContainer()->get('prestashop.adapter.legacy.context');

        Context::setInstanceForTesting($this->context->getContext());

        // Mock the configuration so that unknown keys fall back to the provided default instead of
        // throwing, allowing the full admin request lifecycle - which reads many configuration
        // entries - to run without having to list every single one here.
        $configurationValues = [
            '_PS_MODE_DEMO_' => true,
            '_PS_ROOT_DIR_' => _PS_ROOT_DIR_,
            '_PS_MODULE_DIR_' => _PS_ROOT_DIR_ . '/tests/Resources/modules/',
            '_PS_ALL_THEMES_DIR_' => dirname(__DIR__, 6) . '/themes/',
            'PS_SHOP_DEFAULT' => '1',
            'PS_COOKIE_CHECKIP' => '1',
            'PS_LANG_DEFAULT' => '1',
            'PS_SSL_ENABLED' => '0',
            'PS_CURRENCY_DEFAULT' => '1',
            'PS_COUNTRY_DEFAULT' => '1',
        ];
        $configurationMock = $this->createMock(Configuration::class);
        $configurationMock
            ->method('get')
            ->willReturnCallback(static function ($name, $default = null) use ($configurationValues) {
                return $configurationValues[$name] ?? $default;
            });

        self::$kernel->getContainer()->set('prestashop.adapter.legacy.configuration', $configurationMock);
        self::$kernel->getContainer()->set(ConfigurationInterface::class, $configurationMock);
        self::$kernel->getContainer()->set(Configuration::class, $configurationMock);

        $instancelessModule = $this->createMock(ModuleInterface::class);
        $instancelessModule->method('getInstance')->willReturn(null);

        $moduleRepository = $this->createMock(ModuleRepository::class);
        $moduleRepository->method('getList')->willReturn(new ModuleCollection());
        $moduleRepository->method('getModule')->willReturnCallback(
            function (string $moduleName) use ($instancelessModule): ModuleInterface {
                $this->capturedConfiguredModuleName = $moduleName;

                return $instancelessModule;
            }
        );
        self::$kernel->getContainer()->set(ModuleRepository::class, $moduleRepository);
    }

    public function testModuleAction(): void
    {
        $moduleName = 'test-module';

        $installModuleRoute = $this->router->generate('admin_module_manage_action', [
            'action' => 'install',
            'module_name' => $moduleName,
        ]);
        $this->client->request('POST', $installModuleRoute);

        $response = $this->client->getResponse();
        $responseContent = $response->getContent();

        $decodedContent = json_decode($responseContent, true);

        $this->assertArrayHasKey($moduleName, $decodedContent);

        $this->assertArrayHasKey('status', $decodedContent[$moduleName]);
        $this->assertFalse($decodedContent[$moduleName]['status']);

        $this->assertArrayHasKey('msg', $decodedContent[$moduleName]);

        $this->assertEquals('This functionality has been disabled.', $decodedContent[$moduleName]['msg']);
    }

    public function testImportModuleAction(): void
    {
        $importModuleRoute = $this->router->generate('admin_module_import');
        $this->client->request('POST', $importModuleRoute);

        $response = $this->client->getResponse();
        $responseContent = $response->getContent();

        $decodedContent = json_decode($responseContent, true);

        $this->assertArrayHasKey('msg', $decodedContent);
        $this->assertEquals('This functionality has been disabled.', $decodedContent['msg']);
    }

    /**
     * When the back office security tokens are disabled, admin links are generated without any
     * query string. Modules building their configuration form action by concatenating extra query
     * parameters (e.g. the official GDPR module appends "&page=...") then glue those parameters
     * onto the {module_name} route placeholder instead of the query string. The controller must
     * recover the real module name and must not fail with a 500 error.
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/41314
     */
    public function testConfigureModuleActionRecoversModuleNameWithGluedQueryParameters(): void
    {
        // Disable the back office security tokens so that generated admin links no longer carry a
        // "?_token=..." query string, which is the condition that triggers the bug.
        $previousTokenEnv = getenv(TokenInUrls::ENV_VAR);
        putenv(TokenInUrls::ENV_VAR . '=' . TokenInUrls::DISABLED);

        try {
            // Without a query string on the admin link, a module appending "&page=..." to its form
            // action glues the parameter onto the module_name path segment (no "?" separator).
            $configureUrl = strtok($this->router->generate('admin_module_configure_action', [
                'module_name' => 'ps_emailalerts',
            ]), '?');
            $this->client->request('POST', $configureUrl . '&page=dataConsent');

            // The glued suffix must be stripped before the repository is queried.
            $this->assertSame('ps_emailalerts', $this->capturedConfiguredModuleName);

            // The glued parameters must be re-injected into the request query so the module's own
            // getContent() sees them via Tools::getValue(), otherwise the link points at the
            // module's default configuration page rather than the sub-page it was meant to reach.
            $this->assertSame('dataConsent', $this->client->getRequest()->query->get('page'));

            // A missing module must redirect to the module manager instead of triggering a 500.
            $response = $this->client->getResponse();
            $this->assertTrue(
                $response->isRedirect(),
                'Expected a redirect response, got HTTP ' . $response->getStatusCode()
            );
            $this->assertStringContainsString('manage', (string) $response->headers->get('Location'));
        } finally {
            putenv(TokenInUrls::ENV_VAR . ($previousTokenEnv === false ? '' : '=' . $previousTokenEnv));
        }
    }
}
