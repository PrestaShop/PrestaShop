<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\CspRulesSnapshot;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;

/**
 * The storefront allow-list snapshot: it serves a stored copy without touching csp_rule, and rebuilds from
 * csp_rule (and writes the copy back) on a miss or an explicit refresh.
 */
class CspRulesSnapshotTest extends TestCase
{
    public function testItReturnsTheStoredSnapshotWithoutQueryingTheRulesOnAHit(): void
    {
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturn(json_encode(['script-src' => ['https://cdn.example.com']]));

        $repository = $this->createMock(CspRuleRepository::class);
        $repository->expects($this->never())->method('getRulesByShop');

        $this->assertSame(['script-src' => ['https://cdn.example.com']], (new CspRulesSnapshot($repository, $configuration))->get(1));
    }

    public function testItRebuildsFromTheRulesAndWritesTheCopyBackOnAMiss(): void
    {
        $written = [];
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturn('');
        $configuration->method('set')->willReturnCallback(function (string $key, $value, ?ShopConstraint $scope = null) use (&$written): void {
            $written = ['key' => $key, 'value' => $value, 'scope' => $scope];
        });

        $repository = $this->createMock(CspRuleRepository::class);
        $repository->method('getRulesByShop')->willReturn([
            ['directive' => 'script-src', 'source' => 'https://a.example.com'],
            ['directive' => 'script-src', 'source' => 'https://b.example.com'],
            ['directive' => 'connect-src', 'source' => 'https://api.example.com'],
        ]);

        $snapshot = (new CspRulesSnapshot($repository, $configuration))->get(5);

        $expected = ['script-src' => ['https://a.example.com', 'https://b.example.com'], 'connect-src' => ['https://api.example.com']];
        $this->assertSame($expected, $snapshot);
        // It is stored per shop, at the all-shops scope, so the storefront reads it with no extra query.
        $this->assertSame('PS_CSP_FRONT_RULES_5', $written['key']);
        $this->assertSame(json_encode($expected), $written['value']);
        $this->assertEquals(ShopConstraint::allShops(), $written['scope']);
    }

    public function testRefreshRebuildsAndStoresTheSnapshot(): void
    {
        $written = null;
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('set')->willReturnCallback(function (string $key, $value) use (&$written): void {
            $written = $value;
        });

        $repository = $this->createMock(CspRuleRepository::class);
        $repository->method('getRulesByShop')->willReturn([['directive' => 'img-src', 'source' => 'https://img.example.com']]);

        (new CspRulesSnapshot($repository, $configuration))->refresh(9);

        $this->assertSame(json_encode(['img-src' => ['https://img.example.com']]), $written);
    }
}
