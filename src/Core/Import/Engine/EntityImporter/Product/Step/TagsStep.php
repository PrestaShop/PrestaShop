<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetProductTagsCommand;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\LocalizedValueTrait;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;

/**
 * Replaces the product's tags with the tags cell entries, duplicated into
 * every installed language on creation (single-language-file rule).
 */
class TagsStep extends AbstractProductRowStep
{
    use LocalizedValueTrait;

    public function __construct(
        ValueParser $valueParser,
        protected readonly CommandBusInterface $commandBus,
        protected readonly LanguageRepositoryInterface $languageRepository,
    ) {
        parent::__construct($valueParser);
    }

    public function supports(array $row): bool
    {
        return $this->hasValue($row, 'tags');
    }

    public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array
    {
        $tagsCell = $row['tags'] ?? '';

        $tags = $this->valueParser->split($tagsCell, $context->getMultipleValueSeparator());
        if ([] === $tags) {
            return [];
        }

        $localizedTags = [];
        if ($isCreation) {
            foreach ($this->getAllLanguageIds() as $langId) {
                $localizedTags[$langId] = $tags;
            }
        } else {
            $localizedTags[$languageId] = $tags;
        }

        $this->commandBus->handle(new SetProductTagsCommand($productId, $localizedTags));

        return [];
    }
}
