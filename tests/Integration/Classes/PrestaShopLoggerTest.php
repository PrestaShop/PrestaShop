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
use PrestaShopLogger;
use ReflectionProperty;
use Shop;

class PrestaShopLoggerTest extends TestCase
{
    private const MESSAGE = 'PrestaShopLoggerTest message';

    private int $shopContext;
    private ?int $shopContextId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopContext = Shop::getContext();
        $this->shopContextId = match ($this->shopContext) {
            Shop::CONTEXT_SHOP => (int) Shop::getContextShopID(),
            Shop::CONTEXT_GROUP => (int) Shop::getContextShopGroupID(),
            default => null,
        };
        Shop::setContext(Shop::CONTEXT_SHOP, 1);

        // The minimum level is kept by the class, another test may have raised it
        $minLevel = new ReflectionProperty(PrestaShopLogger::class, 'minLevelInDb');
        $minLevel->setAccessible(true);
        $minLevel->setValue(null, PrestaShopLogger::LOG_SEVERITY_LEVEL_INFORMATIVE);

        PrestaShopLogger::eraseAllLogs();
    }

    protected function tearDown(): void
    {
        Configuration::deleteByName(PrestaShopLogger::DUPLICATE_CHECK_LIMIT_CONFIGURATION);
        Shop::setContext($this->shopContext, $this->shopContextId);
        PrestaShopLogger::eraseAllLogs();
        parent::tearDown();
    }

    public function testSameLogIsStoredOnlyOncePerRequest(): void
    {
        $this->assertTrue(PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR));
        $this->assertFalse(PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR));

        $this->assertSame(1, $this->countLogs());
    }

    public function testDuplicatesAreStoredWhenAllowed(): void
    {
        PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR, null, null, null, true);
        PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR, null, null, null, true);

        $this->assertSame(2, $this->countLogs());
    }

    public function testDifferentLogsAreAllStored(): void
    {
        PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR, null, 'Cart', 1);
        PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR, null, 'Cart', 2);
        PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_MAJOR, null, 'Cart', 2);

        $this->assertSame(3, $this->countLogs());
    }

    public function testLogsWhoseJoinedFieldsLookAlikeAreAllStored(): void
    {
        $this->assertTrue(PrestaShopLogger::addLog('Order 1', PrestaShopLogger::LOG_SEVERITY_LEVEL_WARNING, 30));
        $this->assertTrue(PrestaShopLogger::addLog('Order 12', PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR, 0));

        $this->assertSame(2, $this->countLogs());
    }

    public function testLogsCanBeAddedAgainOnceErased(): void
    {
        PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR);
        PrestaShopLogger::eraseAllLogs();

        $this->assertTrue(PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR));
        $this->assertSame(1, $this->countLogs());
    }

    /**
     * @dataProvider provideShopContexts
     */
    public function testSameLogIsNotStoredAgainByAnotherRequest(int $shopContext, ?int $shopContextId): void
    {
        Shop::setContext($shopContext, $shopContextId);

        PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR, null, 'Cart', 1);
        $this->startNewRequest();

        $this->assertFalse(PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR, null, 'Cart', 1));
        $this->assertTrue(PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR, null, 'Cart', 2));
        $this->assertSame(2, $this->countLogs());
    }

    public function provideShopContexts(): iterable
    {
        yield 'single shop' => [Shop::CONTEXT_SHOP, 1];
        yield 'shop group' => [Shop::CONTEXT_GROUP, 1];
        yield 'all shops' => [Shop::CONTEXT_ALL, null];
    }

    public function testSameLogOfAnotherShopContextIsStored(): void
    {
        PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR);
        $this->startNewRequest();
        Shop::setContext(Shop::CONTEXT_ALL);

        $this->assertTrue(PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR));
        $this->assertSame(2, $this->countLogs());
    }

    public function testLogIsStoredAgainOnceOutOfTheCheckedRange(): void
    {
        Configuration::updateGlobalValue(PrestaShopLogger::DUPLICATE_CHECK_LIMIT_CONFIGURATION, 2);

        PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR);
        PrestaShopLogger::addLog('Another message', PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR);
        $this->startNewRequest();
        $this->assertFalse(PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR));

        PrestaShopLogger::addLog('Yet another message', PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR);
        $this->startNewRequest();
        $this->assertTrue(PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR));
        $this->assertSame(4, $this->countLogs());
    }

    public function testCheckedRangeCountsLogsWhateverTheirIds(): void
    {
        Configuration::updateGlobalValue(PrestaShopLogger::DUPLICATE_CHECK_LIMIT_CONFIGURATION, 2);

        PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR);
        PrestaShopLogger::addLog('Deleted message 1', PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR);
        PrestaShopLogger::addLog('Deleted message 2', PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR);
        PrestaShopLogger::addLog('Another message', PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR);
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'log` WHERE `message` LIKE "Deleted message%"');
        $this->startNewRequest();

        $this->assertFalse(PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR));
    }

    public function testOnlyLogsOfTheRequestAreComparedWhenTheRangeIsZero(): void
    {
        Configuration::updateGlobalValue(PrestaShopLogger::DUPLICATE_CHECK_LIMIT_CONFIGURATION, 0);

        PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR);
        $this->assertFalse(PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR));
        $this->startNewRequest();

        $this->assertTrue(PrestaShopLogger::addLog(self::MESSAGE, PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR));
        $this->assertSame(2, $this->countLogs());
    }

    private function startNewRequest(): void
    {
        $cache = new ReflectionProperty(PrestaShopLogger::class, 'is_present');
        $cache->setAccessible(true);
        $cache->setValue(null, []);
    }

    private function countLogs(): int
    {
        return (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'log`', false);
    }
}
