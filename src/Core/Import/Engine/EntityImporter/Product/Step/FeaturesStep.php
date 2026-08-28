<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\SetProductFeatureValuesCommand;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver\FeatureResolver;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Resolves (or creates) the features and feature values named by the features
 * cell and associates them with the product.
 */
class FeaturesStep extends AbstractProductRowStep
{
    public function __construct(
        ValueParser $valueParser,
        protected readonly FeatureResolver $featureResolver,
        protected readonly CommandBusInterface $commandBus,
        protected readonly TranslatorInterface $translator,
    ) {
        parent::__construct($valueParser);
    }

    public function supports(array $row): bool
    {
        return $this->hasValue($row, 'features');
    }

    public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array
    {
        $messages = [];
        $featuresCell = $row['features'] ?? '';

        $featureValues = [];
        foreach ($this->valueParser->split($featuresCell, $context->getMultipleValueSeparator()) as $entry) {
            // 'Name:Value:Position[:Custom]' — position is ignored (the
            // commands manage positions); this format is the import file's,
            // so parsing it belongs here, not in the resolver
            $parts = array_map('trim', explode(':', $entry));
            $featureName = $parts[0] ?? '';
            $featureValue = $parts[1] ?? '';
            $isCustom = !empty($parts[3]);

            if ('' === $featureName || '' === $featureValue) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Invalid feature entry "%entry%" (expected Name:Value:Position[:Custom]); the entry will be ignored.', ['%entry%' => $entry], 'Admin.Advparameters.Notification'),
                    [$rowIndex],
                    'features'
                );
                continue;
            }

            $feature = $this->featureResolver->resolveFeature($featureName, $languageId, $context);
            if ($feature->isAmbiguous()) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Feature "%name%" matches %count% features; the first one (id %id%) was used.', ['%name%' => $featureName, '%count%' => $feature->matchCount, '%id%' => $feature->id], 'Admin.Advparameters.Notification'),
                    [$rowIndex],
                    'features'
                );
            }
            if ($feature->wasCreated) {
                $messages[] = $this->autoCreationNotice($rowIndex, 'features', $this->translator->trans('Feature "%name%" did not exist and was created.', ['%name%' => $featureName], 'Admin.Advparameters.Notification'));
            }

            if ($isCustom) {
                $featureValues[] = [
                    'feature_id' => $feature->id,
                    'custom_values' => $this->featureResolver->resolveCustomValues($feature->id, $featureValue, $isCreation, $isCreation ? null : $productId, $languageId),
                ];
                continue;
            }

            $value = $this->featureResolver->resolveFeatureValue($feature->id, $featureValue, $languageId);
            if ($value->isAmbiguous()) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Feature value "%value%" matches %count% values of the same feature; the first one (id %id%) was used.', ['%value%' => $featureValue, '%count%' => $value->matchCount, '%id%' => $value->id], 'Admin.Advparameters.Notification'),
                    [$rowIndex],
                    'features'
                );
            }
            if ($value->wasCreated) {
                $messages[] = $this->autoCreationNotice($rowIndex, 'features', $this->translator->trans('Feature value "%value%" did not exist and was created.', ['%value%' => $featureValue], 'Admin.Advparameters.Notification'));
            }
            $featureValues[] = [
                'feature_id' => $feature->id,
                'feature_value_id' => $value->id,
            ];
        }

        if ([] !== $featureValues) {
            $this->commandBus->handle(new SetProductFeatureValuesCommand($productId, $featureValues));
        }

        return $messages;
    }
}
