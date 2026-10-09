<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Service\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Service\Csp\CspTabToggler;

/**
 * Both tabs' visibility must follow the 'csp' feature flag.
 */
class CspTabTogglerTest extends TestCase
{
    /**
     * @dataProvider provideFlagStates
     */
    public function testItSyncsEveryTabStatusToTheFlag(bool $flagEnabled): void
    {
        $flagChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $flagChecker->method('isEnabled')->willReturn($flagEnabled);

        $synced = [];
        $tabRepository = $this->createMock(TabRepository::class);
        $tabRepository->expects($this->exactly(count(CspTabToggler::TAB_CLASS_NAMES)))
            ->method('changeStatusByClassName')
            ->willReturnCallback(function (string $className, bool $enabled) use (&$synced, $flagEnabled): void {
                $this->assertSame($flagEnabled, $enabled);
                $synced[] = $className;
            });

        (new CspTabToggler($flagChecker, $tabRepository))->sync();

        $this->assertSame(CspTabToggler::TAB_CLASS_NAMES, $synced);
    }

    /**
     * @return iterable<string, array{bool}>
     */
    public function provideFlagStates(): iterable
    {
        yield 'flag on enables the tab' => [true];
        yield 'flag off disables the tab' => [false];
    }
}
