<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;

/**
 * Forces the product's creation date to the file's date_add value. The only
 * step bypassing the command bus: no command exposes date_add (it is not a
 * merchant-editable field), so the write goes through the import-only
 * repository fallback. Runs LAST — nothing may write after it.
 */
class DateAddStep extends AbstractProductRowStep
{
    public function __construct(
        ValueParser $valueParser,
        protected readonly ProductRepository $productRepository,
    ) {
        parent::__construct($valueParser);
    }

    public function supports(array $row): bool
    {
        return $this->hasValue($row, 'date_add');
    }

    public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array
    {
        $dateAdd = $this->valueParser->parseDate($row['date_add'] ?? '');
        if (null !== $dateAdd) {
            $this->productRepository->setDateAdd($productId, $dateAdd, $context->getShopConstraint());
        }

        return [];
    }
}
