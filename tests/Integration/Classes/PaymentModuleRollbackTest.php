<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Utility\PaymentModuleFake;

class PaymentModuleRollbackTest extends TestCase
{
    private const PAYMENT_TABLES = ['module_country', 'module_currency', 'module_carrier'];

    private int $moduleId = 0;

    protected function tearDown(): void
    {
        if ($this->moduleId) {
            foreach (array_merge(['module'], self::PAYMENT_TABLES) as $table) {
                Db::getInstance()->delete($table, 'id_module = ' . $this->moduleId);
            }
        }
        parent::tearDown();
    }

    public function testUninstallCoreRegistrationRemovesThePaymentRestrictions(): void
    {
        $db = Db::getInstance();
        self::assertTrue($db->insert('module', ['name' => 'payment_rollback_test', 'active' => 1, 'version' => '1.0.0']));
        $this->moduleId = (int) $db->Insert_ID();

        foreach (self::PAYMENT_TABLES as $table) {
            $column = $table === 'module_carrier' ? 'id_reference' : str_replace('module_', 'id_', $table);
            self::assertTrue($db->insert($table, ['id_module' => $this->moduleId, 'id_shop' => 1, $column => 1]));
        }

        $module = new PaymentModuleFake();
        $module->id = $this->moduleId;

        self::assertTrue($module->uninstallCoreRegistration());

        foreach (self::PAYMENT_TABLES as $table) {
            self::assertSame(
                0,
                (int) $db->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . $table . '` WHERE id_module = ' . $this->moduleId),
                $table . ' still has rows after uninstallCoreRegistration()'
            );
        }
    }
}
