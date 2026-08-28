<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\RemoveAllCustomizationFieldsFromProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\SetProductCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\LocalizedValueTrait;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * uploadable_files and text_fields are integer COUNTS: the row requests
 * N file-upload fields and M text fields (the legacy stored the raw
 * counters without creating any real field — a reviewed fix). The Set
 * command replaces the product's whole field set; an explicit 0/0 on an
 * update therefore removes every existing field. Empty/unmapped cells
 * leave the product untouched.
 */
class CustomizationFieldsStep extends AbstractProductRowStep
{
    use LocalizedValueTrait;

    public function __construct(
        ValueParser $valueParser,
        protected readonly CommandBusInterface $commandBus,
        protected readonly LanguageRepositoryInterface $languageRepository,
        protected readonly TranslatorInterface $translator,
    ) {
        parent::__construct($valueParser);
    }

    public function supports(array $row): bool
    {
        // 'customizable' alone triggers no write, but its inconsistency
        // warning below must still be emitted
        return $this->hasValue($row, 'uploadable_files')
            || $this->hasValue($row, 'text_fields')
            || $this->hasValue($row, 'customizable');
    }

    public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array
    {
        $fileCount = $this->valueParser->parseCount($row['uploadable_files'] ?? '');
        $textCount = $this->valueParser->parseCount($row['text_fields'] ?? '');
        $customizable = true === $this->valueParser->parseBoolean($row['customizable'] ?? '');

        if (null === $fileCount && null === $textCount) {
            if ($customizable) {
                return [new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('"customizable" requires a number of uploadable_files/text_fields; no customization field was created.', [], 'Admin.Advparameters.Notification'),
                    [$rowIndex],
                    'customizable'
                )];
            }

            return [];
        }

        $fileCount ??= 0;
        $textCount ??= 0;

        if (0 === $fileCount && 0 === $textCount) {
            if (!$isCreation) {
                $this->commandBus->handle(new RemoveAllCustomizationFieldsFromProductCommand($productId));
            }

            return [];
        }

        $fields = [];
        $fieldNumber = 0;
        foreach ([CustomizationFieldType::TYPE_FILE => $fileCount, CustomizationFieldType::TYPE_TEXT => $textCount] as $type => $count) {
            for ($i = 0; $i < $count; ++$i) {
                $label = $this->translator->trans('Customization #%number%', ['%number%' => ++$fieldNumber], 'Admin.Global');
                $fields[] = [
                    'type' => $type,
                    'localized_names' => $isCreation ? $this->localizeForCreation($label) : [$languageId => $label],
                    'is_required' => false,
                    'added_by_module' => false,
                ];
            }
        }

        $this->commandBus->handle(new SetProductCustomizationFieldsCommand($productId, $fields, $context->getShopConstraint()));

        return [];
    }
}
