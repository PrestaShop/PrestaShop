<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Db;
use PHPUnit\Framework\TestCase;
use Shop;
use Throwable;
use Tools;

/**
 * Friendly URLs are a per shop setting, so two shops of the same installation may disagree, and
 * the generated .htaccess has to reflect that shop by shop.
 */
class GenerateHtaccessMultishopTest extends TestCase
{
    private const PROBE_URI = '/htaccess-multishop-test/';

    private ?int $probeShopId = null;
    private ?string $multishopBackup = null;
    private string $outputFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->outputFile = sys_get_temp_dir() . '/htaccess_multishop_test_' . getmypid();
        $this->createProbeShop();
    }

    protected function tearDown(): void
    {
        $this->removeProbeShop();
        @unlink($this->outputFile);
        parent::tearDown();
    }

    /**
     * @dataProvider provideSettingsPerShop
     */
    public function testEachShopGetsTheRewriteRulesItsOwnSettingAskedFor(int $mainShopSetting, int $probeShopSetting): void
    {
        $this->writeRewriteSetting(1, $mainShopSetting);
        $this->writeRewriteSetting((int) $this->probeShopId, $probeShopSetting);
        $this->refreshCaches();

        Tools::generateHtaccess($this->outputFile);
        $blocks = $this->rewriteRuleCountPerShop();

        $this->assertSame(
            $mainShopSetting > 0,
            $blocks['/'] > 0,
            'The main shop got the wrong rules for its own setting.'
        );
        $this->assertSame(
            $probeShopSetting > 0,
            $blocks[self::PROBE_URI] > 0,
            'The second shop got rules belonging to another shop.'
        );
        // Both shops share a domain and the dispatcher rules are written once per domain, so
        // they belong there as soon as any shop served by it rewrites its URLs.
        $this->assertSame(
            $mainShopSetting > 0 || $probeShopSetting > 0 ? 1 : 0,
            $this->dispatcherBlockCount(),
            'The dispatcher rules do not match what the shops on this domain asked for.'
        );
    }

    /**
     * @return array<string, array{int, int}>
     */
    public static function provideSettingsPerShop(): array
    {
        return [
            // The order matters: before the fix the first enabled shop pinned the setting for
            // every shop after it, so only this direction was broken.
            'enabled shop first, disabled second' => [1, 0],
            'disabled shop first, enabled second' => [0, 1],
            'both enabled' => [1, 1],
            'both disabled' => [0, 0],
        ];
    }

    public function testAnExplicitSettingFromTheCallerAppliesToEveryShop(): void
    {
        $this->writeRewriteSetting(1, 0);
        $this->writeRewriteSetting((int) $this->probeShopId, 0);
        $this->refreshCaches();

        Tools::generateHtaccess($this->outputFile, 1);
        $blocks = $this->rewriteRuleCountPerShop();

        foreach ($blocks as $base => $count) {
            $this->assertGreaterThan(0, $count, sprintf('%s ignored the value the caller imposed.', $base));
        }
        $this->assertSame(1, $this->dispatcherBlockCount(), 'The imposed value did not reach the dispatcher rules.');
    }

    /**
     * Number of product image rewrite rules written for each shop, keyed by its physical URI.
     *
     * @return array<string, int>
     */
    private function rewriteRuleCountPerShop(): array
    {
        $content = (string) file_get_contents($this->outputFile);
        $counts = [];
        $blocks = preg_split('/^RewriteRule \. - \[E=REWRITEBASE:/m', $content) ?: [];

        foreach ($blocks as $index => $block) {
            if (0 === $index) {
                continue;
            }
            $counts[(string) strtok($block, ']')] = substr_count($block, 'img/p/');
        }

        return $counts;
    }

    /**
     * How many times the per domain dispatcher rules were written.
     */
    private function dispatcherBlockCount(): int
    {
        return substr_count((string) file_get_contents($this->outputFile), '# Send all other traffic to dispatcher');
    }

    private function createProbeShop(): void
    {
        $db = Db::getInstance();
        // The row is not present on every installation, so remember whether it existed at all
        // rather than writing an empty string back over it.
        $storedValue = $db->getValue(
            'SELECT `value` FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` = "PS_MULTISHOP_FEATURE_ACTIVE"'
        );
        $this->multishopBackup = (false === $storedValue || null === $storedValue) ? null : (string) $storedValue;
        if (null === $this->multishopBackup) {
            $db->execute('INSERT INTO `' . _DB_PREFIX_ . 'configuration` (`id_shop_group`, `id_shop`, `name`, `value`, `date_add`, `date_upd`)
                VALUES (NULL, NULL, "PS_MULTISHOP_FEATURE_ACTIVE", "1", NOW(), NOW())');
        } else {
            $db->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET `value` = 1 WHERE `name` = "PS_MULTISHOP_FEATURE_ACTIVE"');
        }

        $db->execute('INSERT INTO `' . _DB_PREFIX_ . 'shop` (`id_shop_group`, `name`, `id_category`, `theme_name`, `active`, `deleted`)
            SELECT `id_shop_group`, "HtaccessMultishopTest", `id_category`, `theme_name`, 1, 0
            FROM `' . _DB_PREFIX_ . 'shop` WHERE `id_shop` = 1');
        $this->probeShopId = (int) $db->Insert_ID();

        $mainUrl = $db->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'shop_url` WHERE `id_shop` = 1');
        $db->execute('INSERT INTO `' . _DB_PREFIX_ . 'shop_url`
            (`id_shop`, `domain`, `domain_ssl`, `physical_uri`, `virtual_uri`, `main`, `active`) VALUES ('
            . (int) $this->probeShopId . ', "' . pSQL($mainUrl['domain']) . '", "' . pSQL($mainUrl['domain_ssl'])
            . '", "' . self::PROBE_URI . '", "", 1, 1)');
    }

    private function removeProbeShop(): void
    {
        $db = Db::getInstance();
        // Each step is independent: one failing must not leave the rest of the fixture behind.
        foreach ([
            fn () => $db->execute('DELETE FROM `' . _DB_PREFIX_ . 'shop_url` WHERE `id_shop` = ' . (int) $this->probeShopId),
            fn () => $db->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration` WHERE `id_shop` = ' . (int) $this->probeShopId),
            fn () => $db->execute('DELETE FROM `' . _DB_PREFIX_ . 'shop` WHERE `id_shop` = ' . (int) $this->probeShopId),
            fn () => $db->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` = "PS_REWRITING_SETTINGS" AND `id_shop` = 1'),
            fn () => null === $this->multishopBackup
                ? $db->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` = "PS_MULTISHOP_FEATURE_ACTIVE"')
                : $db->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET `value` = "'
                    . pSQL((string) $this->multishopBackup) . '" WHERE `name` = "PS_MULTISHOP_FEATURE_ACTIVE"'),
        ] as $step) {
            try {
                $step();
            } catch (Throwable) {
                // keep unwinding the rest of the fixture
            }
        }
        $this->probeShopId = null;
        $this->refreshCaches();
    }

    private function writeRewriteSetting(int $shopId, int $value): void
    {
        $db = Db::getInstance();
        $db->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` = "PS_REWRITING_SETTINGS" AND `id_shop` = ' . $shopId);
        $db->execute('INSERT INTO `' . _DB_PREFIX_ . 'configuration` (`id_shop_group`, `id_shop`, `name`, `value`, `date_add`, `date_upd`)
            VALUES (NULL, ' . $shopId . ', "PS_REWRITING_SETTINGS", "' . $value . '", NOW(), NOW())');
    }

    /**
     * Shop::isFeatureActive() and the configuration values are both resolved once and kept, so a
     * fixture built after boot is invisible until they are dropped.
     */
    private function refreshCaches(): void
    {
        Shop::resetStaticCache();
        Configuration::clearConfigurationCacheForTesting();
        Configuration::loadConfiguration();
    }
}
