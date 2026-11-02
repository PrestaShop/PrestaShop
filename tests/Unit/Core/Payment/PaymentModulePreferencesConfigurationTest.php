<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Payment;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Core\Module\Configuration\PaymentRestrictionsConfiguratorInterface;
use PrestaShop\PrestaShop\Core\Module\Configuration\TradeCreditPaymentModuleConfiguratorInterface;
use PrestaShop\PrestaShop\Core\Module\DataProvider\PaymentModuleListProviderInterface;
use PrestaShop\PrestaShop\Core\Payment\PaymentModulePreferencesConfiguration;

class PaymentModulePreferencesConfigurationTest extends TestCase
{
    private const SHOP_ID = 1;

    private TradeCreditPaymentModuleConfiguratorInterface&MockObject $tradeCreditRepository;

    private PaymentModulePreferencesConfiguration $configuration;

    protected function setUp(): void
    {
        $provider = $this->createMock(PaymentModuleListProviderInterface::class);
        $provider->method('getPaymentModuleList')->willReturn([
            'ps_wirepayment' => $this->paymentModule('ps_wirepayment', 3, true),
            'ps_checkpayment' => $this->paymentModule('ps_checkpayment', 4, false),
        ]);
        $this->tradeCreditRepository = $this->createMock(TradeCreditPaymentModuleConfiguratorInterface::class);

        $this->configuration = new PaymentModulePreferencesConfiguration(
            $provider,
            $this->createMock(PaymentRestrictionsConfiguratorInterface::class),
            $this->tradeCreditRepository,
            self::SHOP_ID
        );
    }

    public function testGetConfigurationContainsTradeCreditModules(): void
    {
        $config = $this->configuration->getConfiguration();

        $this->assertSame(['ps_wirepayment'], $config['trade_credit_modules']);
    }

    public function testUpdateConfigurationSavesTradeCreditModules(): void
    {
        $this->tradeCreditRepository
            ->expects($this->once())
            ->method('setTradeCreditModules')
            ->with(self::SHOP_ID, [4]);

        $this->assertSame([], $this->configuration->updateConfiguration($this->validConfig(['ps_checkpayment'])));
    }

    public function testMissingTradeCreditKeyIsInvalid(): void
    {
        $config = $this->validConfig([]);
        unset($config['trade_credit_modules']);

        $this->assertFalse($this->configuration->validateConfiguration($config));
    }

    private function validConfig(array $tradeCreditModules): array
    {
        return [
            'currency_restrictions' => [],
            'country_restrictions' => [],
            'group_restrictions' => [],
            'carrier_restrictions' => [],
            'trade_credit_modules' => $tradeCreditModules,
        ];
    }

    private function paymentModule(string $name, int $id, bool $isTradeCredit): Module
    {
        return new Module(
            ['name' => $name, 'trade_credit' => $isTradeCredit, 'currencies' => [], 'countries' => [], 'groups' => [], 'carriers' => []],
            [],
            ['id' => $id]
        );
    }
}
