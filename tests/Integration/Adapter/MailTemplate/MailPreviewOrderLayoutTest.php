<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\MailTemplate;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\MailTemplate\MailPreviewVariablesBuilder;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\Layout;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMocker;
use Tests\Resources\DatabaseDump;

/**
 * The order_conf preview is built from the most recent order. Not every order has a usable cart -
 * an order created without one carries id_cart = 0 - and then Cart::getPackageList() is empty.
 * getProductList() read current(current($packageList)) unguarded, so the whole preview died with
 * "current(): Argument #1 ($array) must be of type array, bool given" instead of simply having no
 * product list to show.
 */
class MailPreviewOrderLayoutTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['orders'];

    private Connection $connection;
    private string $dbPrefix;
    private ?ContextMocker $contextMocker = null;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->dbPrefix = self::getContainer()->getParameter('database_prefix');
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        $this->contextMocker = (new ContextMocker())->mockContext();
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);

        if (null !== $this->contextMocker) {
            $this->contextMocker->resetContext();
        }

        parent::tearDown();
    }

    public function testOrderConfirmationPreviewSurvivesAnOrderWithoutACart(): void
    {
        // getOrdersWithInformations() orders by date_add DESC, and ties resolve arbitrarily, so make
        // one order unambiguously the newest and strip its cart.
        $targetOrderId = (int) $this->connection->executeQuery(
            'SELECT id_order FROM ' . $this->dbPrefix . 'orders ORDER BY id_order DESC LIMIT 1'
        )->fetchOne();
        $this->assertGreaterThan(0, $targetOrderId, 'The fixture needs at least one order.');

        $this->connection->executeStatement(
            'UPDATE ' . $this->dbPrefix . 'orders SET id_cart = 0, date_add = NOW() WHERE id_order = :id',
            ['id' => $targetOrderId]
        );

        $builder = self::getContainer()->get(MailPreviewVariablesBuilder::class);
        $variables = $builder->buildTemplateVariables(new Layout('order_conf', '', ''));

        // Reaching this line at all is the regression: without the guard the builder throws.
        $this->assertArrayHasKey('{shop_name}', $variables);
        // The order branch was entered rather than skipped - the product list is simply empty.
        $this->assertArrayHasKey('{products}', $variables);
        $this->assertStringNotContainsString('id_product', (string) $variables['{products}']);
    }
}
