<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Sell\Order;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Tests\Integration\Utility\LoginTrait;
use Tests\Resources\DatabaseDump;

class CreditSlipControllerTest extends WebTestCase
{
    use LoginTrait;
    use PdfDownloadTrait;

    private KernelBrowser $client;

    private RouterInterface $router;

    private CsrfTokenManagerInterface $tokenManager;

    private static int $orderSlipId;

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

        $this->client = self::createClient();
        $this->loginUser($this->client);
        $this->router = self::$kernel->getContainer()->get('router');
        $this->tokenManager = self::$kernel->getContainer()->get(CsrfTokenManagerInterface::class);
    }

    public function testGeneratePdf(): void
    {
        $this->assertPdfResponse('admin_credit_slips_generate_pdf', ['creditSlipId' => self::$orderSlipId]);
    }

    public function testGeneratePdfByDate(): void
    {
        $range = self::getDocumentDateRange();
        $this->submitByDateForm($range['from'], $range['to']);

        $this->assertResponseIsPdf();
    }

    public function testGeneratePdfByDateWithoutCreditSlip(): void
    {
        $this->submitByDateForm('2000-01-01', '2000-01-02');

        $this->assertRedirectsWithError('admin_credit_slips_index');
    }

    private function submitByDateForm(string $dateFrom, string $dateTo): void
    {
        $this->client->request('GET', $this->router->generate('_admin_credit_slips_pdf_by_date', [
            'generate_pdf_by_date' => [
                'from' => $dateFrom,
                'to' => $dateTo,
                '_token' => $this->tokenManager->getToken('generate_pdf_by_date')->getValue(),
            ],
        ]));
    }
}
