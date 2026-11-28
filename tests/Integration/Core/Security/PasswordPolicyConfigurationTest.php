<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Security;

use Configuration as LegacyConfiguration;
use Db;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

final class PasswordPolicyConfigurationTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['configuration']);
        LegacyConfiguration::resetStaticCache();

        parent::tearDown();
    }

    public function testSavingDropsShopAndGroupOverrides(): void
    {
        self::bootKernel();
        foreach ([[1, 1, '3'], [1, null, '4']] as [$shopGroupId, $shopId, $value]) {
            Db::getInstance()->insert('configuration', [
                'name' => PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH,
                'id_shop_group' => $shopGroupId,
                'id_shop' => $shopId,
                'value' => $value,
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd' => date('Y-m-d H:i:s'),
            ], true);
        }
        LegacyConfiguration::resetStaticCache();
        self::assertSame(3, (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` = "' . PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH . '"'
        ), 'global, group and shop rows before saving');

        (new PasswordPolicyConfiguration(self::getContainer()->get('prestashop.adapter.legacy.configuration')))->updateConfiguration([
            'minimum_length' => 10,
            'maximum_length' => 50,
            'minimum_score' => 4,
        ]);

        $rows = Db::getInstance()->executeS(
            'SELECT `id_shop_group`, `id_shop`, `value` FROM `' . _DB_PREFIX_ . 'configuration`
             WHERE `name` = "' . PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH . '"'
        );
        self::assertSame([['id_shop_group' => null, 'id_shop' => null, 'value' => '10']], $rows);
        LegacyConfiguration::resetStaticCache();
        self::assertSame('10', (string) LegacyConfiguration::get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH, null, 1, 1));
    }
}
