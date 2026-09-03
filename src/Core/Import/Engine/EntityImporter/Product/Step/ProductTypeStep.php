<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;

/**
 * On update, the product type is only touched when the is_virtual column
 * is mapped, non-empty AND requests a virtual product — an explicit 0 is
 * left alone so that re-importing a non-virtual file cannot destroy an
 * existing virtual file association (fixes a legacy bug where
 * ProductDownload was deleted on every product row).
 *
 * The command is dispatched ONLY when the type actually changes: converting
 * a product to the type it already has is a no-op the updater would still
 * pay for with an all-shops partial update, and the conversion itself is
 * destructive (see ProductTypeUpdater::updateType() — combinations and pack
 * contents are deleted, stock reset), which the validator warns about.
 *
 * Runs FIRST: the virtual product file step requires an already-virtual
 * product, and creation (which picks its own type) skips this step.
 */
class ProductTypeStep extends AbstractProductRowStep
{
    public function __construct(
        ValueParser $valueParser,
        protected readonly ProductRepository $productRepository,
        protected readonly CommandBusInterface $commandBus,
    ) {
        parent::__construct($valueParser);
    }

    public function supports(array $row): bool
    {
        return $this->hasValue($row, 'is_virtual') && $this->isVirtual($row);
    }

    public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array
    {
        if ($isCreation) {
            return [];
        }

        if (ProductType::TYPE_VIRTUAL === $this->productRepository->getProductType(new ProductId($productId))->getValue()) {
            return [];
        }

        $this->commandBus->handle(new UpdateProductTypeCommand($productId, ProductType::TYPE_VIRTUAL));

        return [];
    }
}
