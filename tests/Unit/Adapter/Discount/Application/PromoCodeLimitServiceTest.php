<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Discount\Application;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Discount\Application\PromoCodeLimitService;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\CartPromoCodeRepository;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

class PromoCodeLimitServiceTest extends TestCase
{
    public function testRejectsAnotherCodeButKeepsTheHighestPriorityExistingCode(): void
    {
        $cartPromoCodes = [
            [
                'id' => 10,
                'discount_type' => DiscountType::CART_LEVEL,
                'priority' => 1,
                'date_add' => '2026-01-01 00:00:00',
            ],
            [
                'id' => 20,
                'discount_type' => DiscountType::CART_LEVEL,
                'priority' => 2,
                'date_add' => '2026-01-02 00:00:00',
            ],
        ];
        $service = $this->createService($cartPromoCodes, false, false);

        $this->assertFalse($service->canApplyPromoCode(1, 2, 30, false));
        $this->assertTrue($service->canApplyPromoCode(1, 2, 10, true));
        $this->assertFalse($service->canApplyPromoCode(1, 2, 20, true));
    }

    public function testConfigurationFallbackKeepsStackingForUpgradedShops(): void
    {
        $repository = $this->createMock(CartPromoCodeRepository::class);
        $repository->expects($this->never())->method('getPromoCodesForCart');

        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->expects($this->once())
            ->method('get')
            ->with('PS_CART_RULE_ALLOW_MULTIPLE_CODES', true, $this->isInstanceOf(ShopConstraint::class))
            ->willReturn(true);

        $featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);

        $service = new PromoCodeLimitService($repository, $configuration, $featureFlagStateChecker);

        $this->assertTrue($service->canApplyPromoCode(1, 2, 30, false));
    }

    public function testNewDiscountSystemPriorityDeterminesWhichLegacyCodeSurvives(): void
    {
        $cartPromoCodes = [
            [
                'id' => 20,
                'discount_type' => DiscountType::FREE_SHIPPING,
                'priority' => 1,
                'date_add' => '2026-01-01 00:00:00',
            ],
            [
                'id' => 10,
                'discount_type' => DiscountType::CART_LEVEL,
                'priority' => 2,
                'date_add' => '2026-01-02 00:00:00',
            ],
        ];
        $service = $this->createService($cartPromoCodes, false, true);

        $this->assertTrue($service->canApplyPromoCode(1, 2, 10, true));
        $this->assertFalse($service->canApplyPromoCode(1, 2, 20, true));
    }

    /**
     * @param array<int, array{id: int, discount_type: string, priority: int, date_add: string}> $cartPromoCodes
     */
    private function createService(array $cartPromoCodes, bool $allowMultiple, bool $useNewDiscountSystem): PromoCodeLimitService
    {
        $repository = $this->createMock(CartPromoCodeRepository::class);
        $expectedRepositoryCalls = $useNewDiscountSystem ? 2 : 3;
        $repository->expects($this->exactly($expectedRepositoryCalls))
            ->method('getPromoCodesForCart')
            ->with(1)
            ->willReturn($cartPromoCodes);

        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')
            ->with('PS_CART_RULE_ALLOW_MULTIPLE_CODES', true, $this->isInstanceOf(ShopConstraint::class))
            ->willReturn($allowMultiple);

        $featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $featureFlagStateChecker->method('isEnabled')
            ->with(FeatureFlagSettings::FEATURE_FLAG_DISCOUNT)
            ->willReturn($useNewDiscountSystem);

        return new PromoCodeLimitService($repository, $configuration, $featureFlagStateChecker);
    }
}
