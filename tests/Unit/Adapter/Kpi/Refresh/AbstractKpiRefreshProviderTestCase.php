<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Stats\Repository\StatsRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Shop\ShopContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Expected values come from the legacy AdminStatsController::displayAjaxGetKpi().
 */
abstract class AbstractKpiRefreshProviderTestCase extends TestCase
{
    protected const SHOP_IDS = [1, 3];
    protected const LANGUAGE_ID = 2;

    protected StatsRepository&MockObject $statsRepository;

    protected ShopContextInterface&MockObject $shopContext;

    protected ConfigurationInterface&MockObject $kpiConfiguration;

    /**
     * @var array<string, mixed> Values written to the KPI configuration, by key
     */
    protected array $cachedValues = [];

    protected function setUp(): void
    {
        $this->statsRepository = $this->createMock(StatsRepository::class);
        $this->shopContext = $this->createMock(ShopContextInterface::class);
        $this->shopContext->method('getContextShopIds')->willReturn(self::SHOP_IDS);
        $this->kpiConfiguration = $this->createMock(ConfigurationInterface::class);
        $this->kpiConfiguration->method('set')->willReturnCallback(function ($key, $value) {
            $this->cachedValues[$key] = $value;

            return $this->kpiConfiguration;
        });
    }

    /**
     * Behaves like the Symfony translator on an untranslated message.
     */
    protected function createTranslator(): TranslatorInterface&MockObject
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(
            static fn (string $id, array $parameters = []) => strtr($id, $parameters)
        );

        return $translator;
    }

    protected function createLanguageContext(): LanguageContext&MockObject
    {
        $languageContext = $this->createMock(LanguageContext::class);
        $languageContext->method('getId')->willReturn(self::LANGUAGE_ID);

        return $languageContext;
    }

    /**
     * @param array<string, string> $values
     */
    protected function createShopConfiguration(array $values): ConfigurationInterface&MockObject
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(static fn ($key) => $values[$key] ?? null);

        return $configuration;
    }

    protected function assertCachedValue(string $key, mixed $expectedValue, string $expectedExpiry): void
    {
        $this->assertSame($expectedValue, $this->cachedValues[$key] ?? null);
        $this->assertEqualsWithDelta(strtotime($expectedExpiry), $this->cachedValues[$key . '_EXPIRE'] ?? 0, 5);
    }

    protected function assertCachedValuePerLanguage(string $key, mixed $expectedValue, string $expectedExpiry): void
    {
        $this->assertSame([self::LANGUAGE_ID => $expectedValue], $this->cachedValues[$key] ?? null);
        $this->assertSame([self::LANGUAGE_ID], array_keys($this->cachedValues[$key . '_EXPIRE'] ?? []));
        $this->assertEqualsWithDelta(strtotime($expectedExpiry), $this->cachedValues[$key . '_EXPIRE'][self::LANGUAGE_ID], 5);
    }
}
