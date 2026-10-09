<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp\CommandHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\CommandHandler\RevokeCspSourceHandler;
use PrestaShop\PrestaShop\Adapter\Csp\CspRulesSnapshotInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\RevokeCspSourceCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use PrestaShopBundle\Entity\CspRule;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;

/**
 * Revocation must stay inside the caller's shop scope: a crafted rule id from another shop is
 * treated as missing so it cannot delete a rule on a shop the admin is not editing.
 */
class RevokeCspSourceHandlerTest extends TestCase
{
    public function testItTreatsARuleFromAnotherShopAsNotFound(): void
    {
        $rule = (new CspRule())->setShopId(2)->setDirective('script-src')->setSource('https://cdn.example.com');

        $repository = $this->createMock(CspRuleRepository::class);
        $repository->method('getById')->with(7)->willReturn($rule);
        // A cross-shop rule must never be deleted.
        $repository->expects($this->never())->method('delete');

        $shopListResolver = $this->createMock(ShopListResolverInterface::class);
        $shopListResolver->method('resolveShopIds')->willReturn([1]);

        $snapshot = $this->createMock(CspRulesSnapshotInterface::class);
        $snapshot->expects($this->never())->method('refresh');

        $this->expectException(CspRuleNotFoundException::class);

        (new RevokeCspSourceHandler($repository, $shopListResolver, $snapshot))
            ->handle(new RevokeCspSourceCommand(7, ShopConstraint::shop(1)));
    }

    public function testItInvalidatesTheStorefrontPolicyCacheWhenRevokingAFrontRule(): void
    {
        $rule = (new CspRule())->setShopId(1)->setContext('front')->setDirective('script-src')->setSource('https://cdn.example.com');

        $repository = $this->createMock(CspRuleRepository::class);
        $repository->method('getById')->willReturn($rule);
        $repository->expects($this->once())->method('delete');

        $shopListResolver = $this->createMock(ShopListResolverInterface::class);
        $shopListResolver->method('resolveShopIds')->willReturn([1]);

        $snapshot = $this->createMock(CspRulesSnapshotInterface::class);
        $snapshot->expects($this->once())->method('refresh')->with(1);

        (new RevokeCspSourceHandler($repository, $shopListResolver, $snapshot))
            ->handle(new RevokeCspSourceCommand(7, ShopConstraint::shop(1)));
    }
}
