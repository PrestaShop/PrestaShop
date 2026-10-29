<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Sell\Order;

use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Tests\Integration\Utility\ContextMockerTrait;
use Tests\Integration\Utility\LoginTrait;
use Tests\Resources\DatabaseDump;

class OrderControllerTest extends WebTestCase
{
    use ContextMockerTrait;
    use LoginTrait;
    use PdfDownloadTrait;

    private static int $orderSlipId;

    /**
     * @var KernelBrowser
     */
    protected $client;
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var CsrfTokenManager
     */
    protected $tokenManager;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$orderSlipId = self::addOrderSlip();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['order_slip']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        // Enable debug mode (for data)
        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->onlyMethods(['get'])
            ->disableOriginalConstructor()
            ->disableAutoload()
            ->getMock();

        $configurationMock->method('get')
            ->will($this->returnValueMap([
                ['_PS_MODE_DEMO_', null, null, true],
            ]));

        $this->client = self::createClient();
        $this->loginUser($this->client);
        self::$kernel->getContainer()->set('prestashop.adapter.legacy.configuration', $configurationMock);
        $this->router = self::$kernel->getContainer()->get('router');
        $this->tokenManager = self::$kernel->getContainer()->get(CsrfTokenManagerInterface::class);
    }

    public function testSearchProductsWithContent(): void
    {
        $this->client->request(
            'GET',
            $this->router->generate(
                'admin_orders_products_search',
                [
                    'search_phrase' => 'Brown bear',
                    'currency_id' => 1,
                    'order_id' => 1,
                ]
            )
        );

        /** @var JsonResponse $response */
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $content = $response->getContent();
        $content = json_decode($content, true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('products', $content);
        $this->assertIsArray($content['products']);
    }

    public function testSearchProductsEmptyPhrase(): void
    {
        $this->client->request(
            'GET',
            $this->router->generate(
                'admin_orders_products_search',
                [
                    'search_phrase' => '',
                    'currency_id' => 1,
                    'order_id' => 1,
                ]
            )
        );

        /** @var JsonResponse $response */
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $content = $response->getContent();
        $content = json_decode($content, true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('Product search phrase must not be an empty string.', $content['message']);
    }

    public function testGenerateCreditSlipPdf(): void
    {
        $this->assertPdfResponse('admin_orders_generate_credit_slip_pdf', ['creditSlipId' => self::$orderSlipId]);
        $this->assertErrorRedirect('admin_orders_generate_credit_slip_pdf', ['creditSlipId' => 999999], 'admin_orders_index');
        $this->assertErrorRedirect('admin_orders_generate_credit_slip_pdf', ['creditSlipId' => 0], 'admin_orders_index');
    }
}
