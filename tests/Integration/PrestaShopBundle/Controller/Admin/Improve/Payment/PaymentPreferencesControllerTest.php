<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Improve\Payment;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Tests\Integration\Utility\LoginTrait;
use Tests\Resources\DatabaseDump;

final class PaymentPreferencesControllerTest extends WebTestCase
{
    use LoginTrait;

    private const RESTRICTION_TABLES = ['module_carrier', 'module_country', 'module_currency', 'module_group'];

    /**
     * @var KernelBrowser
     */
    protected $client;

    /**
     * @var Router
     */
    protected $router;

    protected function setUp(): void
    {
        parent::setUp();
        DatabaseDump::restoreTables(self::RESTRICTION_TABLES);

        $this->client = self::createClient();
        $this->loginUser($this->client);
        $this->router = self::$kernel->getContainer()->get('router');
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(self::RESTRICTION_TABLES);
        parent::tearDown();
    }

    public function testCompleteSubmissionIsSaved(): void
    {
        $values = $this->getSubmittedValues();

        $this->client->request('POST', $this->router->generate('admin_payment_preferences_process'), $values);

        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $flashes = $this->getFlashes();
        $this->assertArrayHasKey('success', $flashes);
        $this->assertArrayNotHasKey('error', $flashes);
    }

    /**
     * When the request exceeds max_input_vars, PHP drops its end: the carrier restrictions, rendered last,
     * and the CSRF token after them. The restrictions must not be replaced with the truncated set.
     */
    public function testTruncatedSubmissionIsNotSaved(): void
    {
        $values = $this->getSubmittedValues();
        $formName = array_key_first($values);
        $this->assertArrayHasKey('carrier_restrictions', $values[$formName]);
        unset($values[$formName]['carrier_restrictions'], $values[$formName]['_token']);
        $carrierRestrictionsBefore = $this->countCarrierRestrictions();
        $this->assertGreaterThan(0, $carrierRestrictionsBefore);

        $this->client->request('POST', $this->router->generate('admin_payment_preferences_process'), $values);

        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $flashes = $this->getFlashes();
        $this->assertArrayNotHasKey('success', $flashes);
        $this->assertArrayHasKey('error', $flashes);
        $this->assertStringContainsString('max_input_vars', $flashes['error'][0]);
        $this->assertSame($carrierRestrictionsBefore, $this->countCarrierRestrictions());
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getSubmittedValues(): array
    {
        $crawler = $this->client->request('GET', $this->router->generate('admin_payment_preferences'));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        return $crawler->filter('#form-carrier-restrictions-save-button')->form()->getPhpValues();
    }

    /**
     * @return array<string, string[]>
     */
    private function getFlashes(): array
    {
        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();

        return $session->getFlashBag()->all();
    }

    private function countCarrierRestrictions(): int
    {
        $container = self::$kernel->getContainer();
        /** @var Connection $connection */
        $connection = $container->get('doctrine.dbal.default_connection');

        return (int) $connection->fetchOne('SELECT COUNT(*) FROM `' . $container->getParameter('database_prefix') . 'module_carrier`');
    }
}
