<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationCommandsBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataFormatter\CombinationListFormDataFormatter;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\CombinationListFormDataHandler;

class CombinationListFormDataHandlerTest extends TestCase
{
    public function testUncheckedStatusDisablesTheCombinationWhenFlagIsEnabled(): void
    {
        $this->assertSame(['header' => ['active' => false]], $this->getBuiltFormData(true));
    }

    public function testStatusIsIgnoredWhenFlagIsDisabled(): void
    {
        $this->assertSame([], $this->getBuiltFormData(false));
    }

    private function getBuiltFormData(bool $isFlagEnabled): array
    {
        $featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $featureFlagStateChecker
            ->method('isEnabled')
            ->with(FeatureFlagSettings::FEATURE_FLAG_COMBINATION_STATUS)
            ->willReturn($isFlagEnabled)
        ;

        $builtFormData = null;
        $commandsBuilder = $this->createMock(CombinationCommandsBuilderInterface::class);
        $commandsBuilder
            ->method('buildCommands')
            ->willReturnCallback(function ($combinationId, array $formData) use (&$builtFormData): array {
                $builtFormData = $formData;

                return [];
            })
        ;

        $handler = new CombinationListFormDataHandler(
            $this->createMock(CommandBusInterface::class),
            new CombinationListFormDataFormatter('modify_all_shops_'),
            $commandsBuilder,
            1,
            1,
            $featureFlagStateChecker
        );
        $handler->update(42, [['combination_id' => 7]]);

        return $builtFormData;
    }
}
