<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\PDF;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;
use PrestaShop\PrestaShop\Adapter\PDF\TwigPdfRendererChecker;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

class TwigPdfRendererCheckerTest extends TestCase
{
    public function testIsEnabledReturnsFalseWhenFeatureFlagIsDisabled(): void
    {
        $hookInformationProvider = $this->createMock(HookInformationProvider::class);
        $hookInformationProvider->expects($this->never())->method('getRegisteredModulesByHookName');

        $checker = new TwigPdfRendererChecker($this->createFeatureFlagStateChecker(false), $hookInformationProvider);

        $this->assertFalse($checker->isEnabled());
    }

    public function testIsEnabledReturnsTrueWhenFeatureFlagIsEnabledAndNoLegacyHookIsUsed(): void
    {
        $hookInformationProvider = $this->createMock(HookInformationProvider::class);
        $hookInformationProvider->method('getRegisteredModulesByHookName')->willReturn([]);

        $checker = new TwigPdfRendererChecker($this->createFeatureFlagStateChecker(true), $hookInformationProvider);

        $this->assertTrue($checker->isEnabled());
    }

    /**
     * @dataProvider provideLegacyCustomizationHooks
     */
    public function testIsEnabledReturnsFalseWhenALegacyCustomizationHookIsUsed(string $hookedName): void
    {
        $hookInformationProvider = $this->createMock(HookInformationProvider::class);
        $hookInformationProvider->method('getRegisteredModulesByHookName')
            ->willReturnCallback(static fn (string $hookName): array => $hookName === $hookedName ? [['module' => 'custompdf']] : []);

        $checker = new TwigPdfRendererChecker($this->createFeatureFlagStateChecker(true), $hookInformationProvider);

        $this->assertFalse($checker->isEnabled());
    }

    public static function provideLegacyCustomizationHooks(): iterable
    {
        yield 'custom renderer' => ['actionGetPdfRenderer'];
        yield 'custom template object' => ['actionGetPdfTemplateObject'];
    }

    private function createFeatureFlagStateChecker(bool $enabled): FeatureFlagStateCheckerInterface
    {
        $featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $featureFlagStateChecker->method('isEnabled')
            ->with(FeatureFlagSettings::FEATURE_FLAG_PDF_TWIG_RENDERER)
            ->willReturn($enabled);

        return $featureFlagStateChecker;
    }
}
