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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Tests\Integration\Utility\LoginTrait;

class DeliveryControllerTest extends WebTestCase
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
     * @var CsrfTokenManager
     */
    protected $tokenManager;

    protected function setUp(): void
    {
        parent::setUp();

        // Enable debug mode
        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->onlyMethods(['get'])
            ->disableOriginalConstructor()
            ->disableAutoload()
            ->getMock();

        $values = [
            ['_PS_MODE_DEMO_', null, null, true],
            ['_PS_MODULE_DIR_', null, null, dirname(__DIR__, 3) . '/resources/modules/'],
        ];

        $configurationMock->method('get')
            ->will($this->returnValueMap($values));

        $this->client = self::createClient();
        $this->loginUser($this->client);
        self::$kernel->getContainer()->set('prestashop.adapter.legacy.configuration', $configurationMock);
        $this->router = self::$kernel->getContainer()->get('router');
        $this->tokenManager = self::$kernel->getContainer()->get(CsrfTokenManagerInterface::class);
    }

    public function testSlipAction(): void
    {
        $this->client->request(
            'GET',
            $this->router->generate(
                'admin_order_delivery_slip'
            )
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testSlipActionWithInvalidData(): void
    {
        $token = $this->tokenManager->getToken('form');
        $this->client->request(
            'POST',
            $this->router->generate(
                'admin_order_delivery_slip'
            ),
            [
                'form' => [
                    'number' => 'foo',
                    '_token' => $token->getValue(),
                ],
            ]
        );
        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );
        $this->assertStringContainsString('Please enter a number.', $response->getContent());
    }

    public function testSlipActionWithValidData(): void
    {
        $token = $this->tokenManager->getToken('form');
        $this->client->request(
            'POST',
            $this->router->generate(
                'admin_order_delivery_slip'
            ),
            [
                'form' => [
                    'number' => '100',
                    '_token' => $token->getValue(),
                ],
            ]
        );
        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_FOUND,
            $response->getStatusCode()
        );

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $this->assertArrayHasKey(
            'success',
            $session->getFlashBag()->all()
        );
    }

    public function testPdfActionWithInvalidData(): void
    {
        $token = $this->tokenManager->getToken('slip_pdf_form');
        $this->client->request(
            'POST',
            $this->router->generate(
                'admin_order_delivery_slip_pdf'
            ),
            [
                'slip_pdf_form' => [
                    'date_from' => 'foo',
                    '_token' => $token->getValue(),
                ],
            ]
        );
        /** @var RedirectResponse $response */
        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_FOUND,
            $response->getStatusCode()
        );
        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $this->assertArrayHasKey(
            'error',
            $session->getFlashBag()->all()
        );
        $this->assertStringContainsString('/sell/orders/delivery-slips/?_token', $response->getTargetUrl());
    }

    public function testPdfActionWithEmptyData(): void
    {
        $token = $this->tokenManager->getToken('slip_pdf_form');
        $this->client->request(
            'POST',
            $this->router->generate(
                'admin_order_delivery_slip_pdf'
            ),
            [
                'slip_pdf_form' => [
                    '_token' => $token->getValue(),
                ],
            ]
        );
        /** @var RedirectResponse $response */
        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_FOUND,
            $response->getStatusCode()
        );

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $this->assertArrayHasKey(
            'error',
            $session->getFlashBag()->all()
        );
        $this->assertStringContainsString('/sell/orders/delivery-slips/?_token', $response->getTargetUrl());
    }
}
