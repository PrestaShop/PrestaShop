<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Sell\Order;

use OrderInvoice;
use OrderSlip;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Test order 1 has no document in the fixtures, these helpers add the ones the PDF actions need.
 * The using test case must have the $client (KernelBrowser) and $router (RouterInterface) properties.
 */
trait PdfDownloadTrait
{
    /**
     * Away from today, which the delivery slip form submits by default
     */
    private static string $documentDate = '2020-01-15 10:00:00';

    private static function addOrderInvoice(): int
    {
        $orderInvoice = new OrderInvoice();
        $orderInvoice->id_order = 1;
        $orderInvoice->number = 1;
        $orderInvoice->delivery_date = self::$documentDate;
        $orderInvoice->add();
        // OrderInvoice::add() always sets the current date
        $orderInvoice->date_add = self::$documentDate;
        $orderInvoice->update();

        return (int) $orderInvoice->id;
    }

    private static function addOrderSlip(): int
    {
        $orderSlip = new OrderSlip();
        $orderSlip->id_customer = 1;
        $orderSlip->id_order = 1;
        $orderSlip->conversion_rate = 1;
        $orderSlip->total_products_tax_excl = 0;
        $orderSlip->total_products_tax_incl = 0;
        $orderSlip->total_shipping_tax_excl = 0;
        $orderSlip->total_shipping_tax_incl = 0;
        $orderSlip->date_add = $orderSlip->date_upd = self::$documentDate;
        $orderSlip->add(false);

        return (int) $orderSlip->id;
    }

    /**
     * @return array{from: string, to: string}
     */
    private static function getDocumentDateRange(): array
    {
        $date = substr(self::$documentDate, 0, 10);

        return ['from' => $date, 'to' => $date];
    }

    private function assertPdfResponse(string $route, array $parameters): void
    {
        $this->client->request('GET', $this->router->generate($route, $parameters));

        $this->assertResponseIsPdf();
    }

    private function assertResponseIsPdf(): void
    {
        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('attachment;', (string) $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF', (string) $response->getContent());
    }

    private function assertErrorRedirect(string $route, array $parameters, string $expectedRoute): void
    {
        $this->client->request('GET', $this->router->generate($route, $parameters));

        $this->assertRedirectsWithError($expectedRoute);
    }

    private function assertRedirectsWithError(string $expectedRoute): void
    {
        /** @var RedirectResponse $response */
        $response = $this->client->getResponse();
        $this->assertInstanceOf(RedirectResponse::class, $response);
        // Admin URLs carry a random CSRF token, only the paths are compared
        $this->assertSame(
            parse_url($this->router->generate($expectedRoute), PHP_URL_PATH),
            parse_url($response->getTargetUrl(), PHP_URL_PATH)
        );

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $this->assertNotEmpty($session->getFlashBag()->get('error'));
    }
}
