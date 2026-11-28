<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Sell\Order;

use Order;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Tests\Integration\Utility\LoginTrait;
use Tests\Resources\DatabaseDump;

class InvoicesControllerTest extends WebTestCase
{
    use LoginTrait;
    use PdfDownloadTrait;

    private KernelBrowser $client;

    private RouterInterface $router;

    private CsrfTokenManagerInterface $tokenManager;

    private static int $orderStateId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::addOrderInvoice();
        self::$orderStateId = (int) (new Order(1))->current_state;
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['order_invoice']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
        $this->loginUser($this->client);
        $this->router = self::$kernel->getContainer()->get('router');
        $this->tokenManager = self::$kernel->getContainer()->get(CsrfTokenManagerInterface::class);
    }

    public function testGeneratePdfByDate(): void
    {
        $range = self::getDocumentDateRange();
        $this->submitByDateForm($range['from'], $range['to']);

        $this->assertResponseIsPdf();
    }

    /**
     * @dataProvider getInvalidDateRanges
     */
    public function testGeneratePdfByDateWithInvalidRange(string $dateFrom, string $dateTo): void
    {
        $this->submitByDateForm($dateFrom, $dateTo);

        $this->assertRedirectsWithError('admin_order_invoices');
    }

    public static function getInvalidDateRanges(): iterable
    {
        yield 'no invoice in the range' => ['2000-01-01', '2000-01-02'];
        yield 'empty date' => ['', '2000-01-02'];
        yield 'malformed date' => ['2000-01-01', 'foo'];
    }

    public function testGeneratePdfByStatus(): void
    {
        $this->submitByStatusForm([self::$orderStateId]);

        $this->assertResponseIsPdf();
    }

    public function testGeneratePdfByStatusWithoutStatus(): void
    {
        $this->submitByStatusForm([]);

        $this->assertRedirectsWithError('admin_order_invoices');
    }

    private function submitByDateForm(string $dateFrom, string $dateTo): void
    {
        $this->client->request('POST', $this->router->generate('admin_order_invoices_generate_by_date'), [
            'form' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                '_token' => $this->tokenManager->getToken('form')->getValue(),
            ],
        ]);
    }

    /**
     * @param int[] $orderStateIds
     */
    private function submitByStatusForm(array $orderStateIds): void
    {
        $this->client->request('POST', $this->router->generate('admin_order_invoices_generate_by_status'), [
            'form' => [
                'order_states' => $orderStateIds,
                '_token' => $this->tokenManager->getToken('form')->getValue(),
            ],
        ]);
    }
}
