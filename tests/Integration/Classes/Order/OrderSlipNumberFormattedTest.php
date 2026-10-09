<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Order;

use Cache;
use Configuration;
use Doctrine\DBAL\Connection;
use Hook;
use Module;
use Order;
use OrderSlip;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\OrderDocumentType;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMocker;
use Tests\Resources\DatabaseDump;

/**
 * Invoices could already have their number reformatted by a module through
 * actionInvoiceNumberFormatted; credit slips had no equivalent, and each of the three places that
 * showed a credit slip number built "prefix + %06d" itself.
 */
class OrderSlipNumberFormattedTest extends KernelTestCase
{
    private const MODULE_NAME = 'ps_creditslipnumbertest';
    private const FORMATTED_NUMBER = 'CN-TEST-0001';

    private const TABLES_TO_RESTORE = [
        'order_slip',
        'module',
        'module_shop',
        'hook_module',
        'module_group',
        'authorization_role',
        'module_access',
    ];

    private Connection $connection;
    private string $dbPrefix;
    private CommandBusInterface $queryBus;
    private int $orderId;
    private int $orderSlipId;
    private ?ContextMocker $contextMocker = null;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->dbPrefix = self::getContainer()->getParameter('database_prefix');
        $this->queryBus = self::getContainer()->get('prestashop.core.query_bus');

        $this->contextMocker = (new ContextMocker())->mockContext();

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        Module::resetStaticCache();
        Cache::clean(Hook::MODULE_LIST_BY_HOOK_KEY . '*');

        $this->orderId = (int) $this->connection->executeQuery(
            'SELECT id_order FROM ' . $this->dbPrefix . 'orders ORDER BY id_order ASC LIMIT 1'
        )->fetchOne();
        $this->orderSlipId = $this->insertOrderSlip($this->orderId);
    }

    protected function tearDown(): void
    {
        $moduleManager = ModuleManagerBuilder::getInstance()->build();
        if ($moduleManager->isInstalled(self::MODULE_NAME)) {
            $module = Module::getInstanceByName(self::MODULE_NAME);
            if ($module instanceof Module) {
                $module->uninstall();
            }
        }

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);

        if (null !== $this->contextMocker) {
            $this->contextMocker->resetContext();
        }

        parent::tearDown();
    }

    public function testTheDefaultFormatIsThePrefixFollowedByThePaddedId(): void
    {
        $orderSlip = new OrderSlip($this->orderSlipId);
        $order = new Order($this->orderId);
        $languageId = (int) Configuration::get('PS_LANG_DEFAULT');

        $expected = sprintf(
            '%1$s%2$06d',
            Configuration::get('PS_CREDIT_SLIP_PREFIX', $languageId, null, (int) $order->id_shop),
            $this->orderSlipId
        );

        $this->assertSame($expected, $orderSlip->getCreditSlipNumberFormatted($languageId, (int) $order->id_shop));
    }

    public function testAModuleCanReformatTheNumber(): void
    {
        $this->installModule();

        $orderSlip = new OrderSlip($this->orderSlipId);
        $order = new Order($this->orderId);

        $this->assertSame(
            self::FORMATTED_NUMBER,
            $orderSlip->getCreditSlipNumberFormatted((int) Configuration::get('PS_LANG_DEFAULT'), (int) $order->id_shop)
        );
    }

    /**
     * The hop that matters: the number a merchant actually reads, in the order view's Documents tab.
     */
    public function testTheReformattedNumberReachesTheOrderView(): void
    {
        $this->assertSame(self::FORMATTED_NUMBER, $this->creditSlipNumberInOrderView(), 'control');
    }

    private function creditSlipNumberInOrderView(): ?string
    {
        $this->installModule();

        $orderForViewing = $this->queryBus->handle(new GetOrderForViewing($this->orderId));

        foreach ($orderForViewing->getDocuments()->getDocuments() as $document) {
            if (OrderDocumentType::CREDIT_SLIP === $document->getType()) {
                return $document->getReferenceNumber();
            }
        }

        return null;
    }

    private function insertOrderSlip(int $orderId): int
    {
        $order = new Order($orderId);

        $this->connection->executeStatement(
            'INSERT INTO ' . $this->dbPrefix . 'order_slip
                (conversion_rate, id_customer, id_order, total_products_tax_excl, total_products_tax_incl,
                 total_shipping_tax_excl, total_shipping_tax_incl, amount, shipping_cost, shipping_cost_amount,
                 partial, date_add, date_upd)
             VALUES (1, :customer, :order, 10, 10, 0, 0, 10, 0, 0, 0, NOW(), NOW())',
            ['customer' => (int) $order->id_customer, 'order' => $orderId]
        );

        return (int) $this->connection->lastInsertId();
    }

    private function installModule(): void
    {
        // restoreTables() wipes the module rows underneath the in-process caches, so a stale "already
        // installed" answer would silently skip the registration and leave the hook with no listener.
        Module::resetStaticCache();

        $moduleManager = ModuleManagerBuilder::getInstance()->build();
        $this->assertTrue((bool) $moduleManager->install(self::MODULE_NAME));

        Cache::clean(Hook::MODULE_LIST_BY_HOOK_KEY . '*');
        Module::resetStaticCache();
    }
}
