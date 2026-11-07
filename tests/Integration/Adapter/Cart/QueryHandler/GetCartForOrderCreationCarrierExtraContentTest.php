<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Cart\QueryHandler;

use Cache;
use Carrier;
use Context;
use Module;
use PrestaShop\PrestaShop\Adapter\Cart\QueryHandler\GetCartForOrderCreationHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartDeliveryOption;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The manual order form runs a carrier module's `displayCarrierExtraContent` hook, the way the
 * front office delivery step does, so the module can render its own picker in the back office.
 *
 * A probe module is installed on disk and registered on that hook for the duration of these
 * tests: without a carrier module that actually answers the hook, every assertion here would
 * hold whether or not the dispatch happens at all.
 */
class GetCartForOrderCreationCarrierExtraContentTest extends KernelTestCase
{
    private const PROBE = 'probecarrier16131';
    private const MARKUP = '<div class="probe-16131">pickup point picker</div>';

    private $connection;

    private int $probeModuleId = 0;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        // The legacy objects this handler builds reach for the container through the context.
        Context::getContext()->container = $container;
        $this->connection = $container->get('doctrine.dbal.default_connection');

        $this->writeProbeModuleFile();

        $this->connection->executeStatement(
            'INSERT INTO ' . _DB_PREFIX_ . 'module (name, active, version) VALUES (:n, 1, :v)',
            ['n' => self::PROBE, 'v' => '1.0.0']
        );
        $this->probeModuleId = (int) $this->connection->lastInsertId();

        $hookId = (int) $this->connection->executeQuery(
            'SELECT id_hook FROM ' . _DB_PREFIX_ . "hook WHERE name = 'displayCarrierExtraContent'"
        )->fetchOne();
        $this->assertGreaterThan(0, $hookId, 'The displayCarrierExtraContent hook must exist.');

        // Registered for every shop: the hook's module list joins module_shop and filters it by
        // the context shop list, which a CLI run does not necessarily set to shop 1.
        foreach ($this->connection->executeQuery('SELECT id_shop FROM ' . _DB_PREFIX_ . 'shop')->fetchFirstColumn() as $shopId) {
            $this->connection->executeStatement(
                'INSERT INTO ' . _DB_PREFIX_ . 'module_shop (id_module, id_shop, enable_device) VALUES (:m, :s, 7)',
                ['m' => $this->probeModuleId, 's' => (int) $shopId]
            );
            $this->connection->executeStatement(
                'INSERT INTO ' . _DB_PREFIX_ . 'hook_module (id_module, id_shop, id_hook, position) VALUES (:m, :s, :h, 1)',
                ['m' => $this->probeModuleId, 's' => (int) $shopId, 'h' => $hookId]
            );
        }

        $this->resetCaches();
    }

    protected function tearDown(): void
    {
        if ($this->probeModuleId) {
            foreach (['hook_module', 'module_shop'] as $table) {
                $this->connection->executeStatement(
                    'DELETE FROM ' . _DB_PREFIX_ . $table . ' WHERE id_module = :m',
                    ['m' => $this->probeModuleId]
                );
            }
            $this->connection->executeStatement(
                'DELETE FROM ' . _DB_PREFIX_ . 'module WHERE id_module = :m',
                ['m' => $this->probeModuleId]
            );
            $this->probeModuleId = 0;
        }
        $this->removeProbeModuleFile();
        $this->resetCaches();

        parent::tearDown();
    }

    public function testItRendersTheCarrierModulesOwnContent(): void
    {
        $carrier = new Carrier();
        $carrier->is_module = true;
        $carrier->external_module_name = self::PROBE;

        $this->assertSame(self::MARKUP, $this->getCarrierExtraContent($carrier));
    }

    public function testItLeavesACarrierThatBelongsToNoModuleAlone(): void
    {
        // Same module name, only the flag differs, so the empty result can come from nothing else.
        $carrier = new Carrier();
        $carrier->is_module = false;
        $carrier->external_module_name = self::PROBE;

        $this->assertSame('', $this->getCarrierExtraContent($carrier));
    }

    public function testItSkipsTheHookWhenTheCarriersModuleCannotBeResolved(): void
    {
        // Without the id the hook would go to every module registered on it - the probe among
        // them - instead of the carrier's own module.
        $carrier = new Carrier();
        $carrier->is_module = true;
        $carrier->external_module_name = 'a_module_that_is_not_installed';

        $this->assertSame('', $this->getCarrierExtraContent($carrier));
    }

    public function testEveryDeliveryOptionCarriesTheField(): void
    {
        $cartId = (int) $this->connection->executeQuery(
            'SELECT c.id_cart
               FROM ' . _DB_PREFIX_ . 'cart c
              WHERE c.id_customer > 0
                AND c.id_address_delivery > 0
                AND (SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'cart_product cp WHERE cp.id_cart = c.id_cart) > 0
              ORDER BY c.id_cart
              LIMIT 1'
        )->fetchOne();

        if (!$cartId) {
            self::markTestSkipped('The fixtures hold no cart with a delivery address and a product.');
        }

        $shipping = self::getContainer()->get('prestashop.core.query_bus')
            ->handle(new GetCartForOrderCreation($cartId))
            ->getShipping();

        if (null === $shipping) {
            self::markTestSkipped(sprintf('Cart %d resolves to no shipping block.', $cartId));
        }

        $options = $shipping->getDeliveryOptions();
        $this->assertNotEmpty($options, 'Expected at least one delivery option to inspect.');
        foreach ($options as $option) {
            $this->assertInstanceOf(CartDeliveryOption::class, $option);
            // Native carriers belong to no module, so the field is empty; what this pins is that
            // the handler fills it in and the value object carries it to the query result.
            $this->assertSame('', $option->getExtraContent());
        }
    }

    private function getCarrierExtraContent(Carrier $carrier): string
    {
        $container = self::getContainer();
        $handler = new GetCartForOrderCreationHandler(
            $container->get('prestashop.core.localization.locale.context_locale'),
            (int) Context::getContext()->language->id,
            Context::getContext()->link,
            $container->get('prestashop.adapter.context_state_manager'),
            0
        );

        $method = new ReflectionMethod($handler, 'getCarrierExtraContent');
        $method->setAccessible(true);

        return $method->invoke($handler, $carrier);
    }

    private function probeModuleDir(): string
    {
        return _PS_MODULE_DIR_ . self::PROBE;
    }

    private function writeProbeModuleFile(): void
    {
        if (!is_dir($this->probeModuleDir())) {
            mkdir($this->probeModuleDir(), 0777, true);
        }
        file_put_contents(
            $this->probeModuleDir() . '/' . self::PROBE . '.php',
            '<?php' . PHP_EOL
            . 'class ' . self::PROBE . ' extends Module' . PHP_EOL
            . '{' . PHP_EOL
            . '    public function __construct()' . PHP_EOL
            . '    {' . PHP_EOL
            . "        \$this->name = '" . self::PROBE . "';" . PHP_EOL
            . "        \$this->version = '1.0.0';" . PHP_EOL
            . "        \$this->author = 'PrestaShop tests';" . PHP_EOL
            . '        parent::__construct();' . PHP_EOL
            . "        \$this->displayName = 'Carrier extra content probe';" . PHP_EOL
            . "        \$this->description = 'Fixture for GetCartForOrderCreationCarrierExtraContentTest.';" . PHP_EOL
            . '    }' . PHP_EOL
            . PHP_EOL
            . '    public function hookDisplayCarrierExtraContent($params)' . PHP_EOL
            . '    {' . PHP_EOL
            . "        return '" . self::MARKUP . "';" . PHP_EOL
            . '    }' . PHP_EOL
            . '}' . PHP_EOL
        );
    }

    private function removeProbeModuleFile(): void
    {
        $file = $this->probeModuleDir() . '/' . self::PROBE . '.php';
        if (is_file($file)) {
            unlink($file);
        }
        if (is_dir($this->probeModuleDir())) {
            rmdir($this->probeModuleDir());
        }
    }

    private function resetCaches(): void
    {
        Module::resetStaticCache();
        Cache::clean('*');
    }
}
