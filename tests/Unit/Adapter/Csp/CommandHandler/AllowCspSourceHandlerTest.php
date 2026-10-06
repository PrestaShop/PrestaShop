<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp\CommandHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\CommandHandler\AllowCspSourceHandler;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\AllowCspSourceCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspLogNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use PrestaShopBundle\Entity\CspLog;
use PrestaShopBundle\Entity\Repository\CspLogRepository;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;

/**
 * The curate action must stay inside the caller's shop scope: a crafted log id from another shop
 * is treated as missing so it cannot allow a source on a shop the admin is not editing.
 */
class AllowCspSourceHandlerTest extends TestCase
{
    public function testItTreatsALogFromAnotherShopAsNotFound(): void
    {
        $log = (new CspLog())->setShopId(2)->setDirective('script-src')->setSource('https://cdn.example.com');

        $logRepository = $this->createMock(CspLogRepository::class);
        $logRepository->method('find')->with(42)->willReturn($log);

        $ruleRepository = $this->createMock(CspRuleRepository::class);
        // A cross-shop log must never be promoted to a rule.
        $ruleRepository->expects($this->never())->method('add');

        $this->expectException(CspLogNotFoundException::class);

        $this->handler($logRepository, $ruleRepository, resolvedShopIds: [1])
            ->handle(new AllowCspSourceCommand(42, ShopConstraint::shop(1)));
    }

    public function testItTreatsAMissingLogAsNotFound(): void
    {
        $logRepository = $this->createMock(CspLogRepository::class);
        $logRepository->method('find')->willReturn(null);

        $this->expectException(CspLogNotFoundException::class);

        $this->handler($logRepository, $this->createMock(CspRuleRepository::class), resolvedShopIds: [1])
            ->handle(new AllowCspSourceCommand(42, ShopConstraint::shop(1)));
    }

    public function testItAllowsABackOfficeLogUnderTheGlobalShopZero(): void
    {
        $log = (new CspLog())->setShopId(0)->setContext('admin')->setDirective('script-src')->setSource('https://admin.example.com');

        $logRepository = $this->createMock(CspLogRepository::class);
        $logRepository->method('find')->with(42)->willReturn($log);

        $ruleRepository = $this->createMock(CspRuleRepository::class);
        $ruleRepository->method('findOneByShopDirectiveSource')->willReturn(null);
        $ruleRepository->expects($this->once())->method('add')->willReturn(7);

        $ruleId = $this->handler($logRepository, $ruleRepository, resolvedShopIds: [])
            ->handle(new AllowCspSourceCommand(42, ShopConstraint::allShops(), CspContext::ADMIN));

        $this->assertSame(7, $ruleId->getValue());
    }

    public function testItTreatsAStorefrontLogAsNotFoundOnTheBackOfficeSurface(): void
    {
        // A storefront log (shop id >= 1) must not be curatable from the back office (global shop id 0).
        $log = (new CspLog())->setShopId(2)->setContext('front')->setDirective('script-src')->setSource('https://cdn.example.com');

        $logRepository = $this->createMock(CspLogRepository::class);
        $logRepository->method('find')->willReturn($log);

        $ruleRepository = $this->createMock(CspRuleRepository::class);
        $ruleRepository->expects($this->never())->method('add');

        $this->expectException(CspLogNotFoundException::class);

        $this->handler($logRepository, $ruleRepository, resolvedShopIds: [])
            ->handle(new AllowCspSourceCommand(42, ShopConstraint::allShops(), CspContext::ADMIN));
    }

    /**
     * @param list<int> $resolvedShopIds
     */
    private function handler(CspLogRepository $logRepository, CspRuleRepository $ruleRepository, array $resolvedShopIds): AllowCspSourceHandler
    {
        $shopListResolver = $this->createMock(ShopListResolverInterface::class);
        $shopListResolver->method('resolveShopIds')->willReturn($resolvedShopIds);

        return new AllowCspSourceHandler($logRepository, $ruleRepository, $shopListResolver);
    }
}
