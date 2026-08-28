<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;

/**
 * Stock location, out-of-stock behavior and quantity. The stock command only
 * expresses deltas, so the file's absolute quantity is converted against the
 * current stock row — the step must therefore run after the product (and its
 * stock row) exists.
 */
class StockStep extends AbstractProductRowStep
{
    public function __construct(
        ValueParser $valueParser,
        protected readonly StockAvailableRepository $stockAvailableRepository,
        protected readonly CommandBusInterface $commandBus,
    ) {
        parent::__construct($valueParser);
    }

    public function supports(array $row): bool
    {
        // no cheap row-only guard: apply() gates every field itself and
        // dispatches nothing when no stock cell parses
        return true;
    }

    public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array
    {
        $command = new UpdateProductStockAvailableCommand($productId, $context->getShopConstraint());
        $hasUpdate = false;

        if ($this->hasValue($row, 'location')) {
            $command->setLocation($row['location']);
            $hasUpdate = true;
        }
        // strict integer parsing first: '(int) "abc"' would silently become
        // a valid enum value 0
        $outOfStock = $this->valueParser->parseInteger($row['out_of_stock'] ?? '');
        if (null !== $outOfStock && in_array($outOfStock, [0, 1, 2], true)) {
            $command->setOutOfStockType($outOfStock);
            $hasUpdate = true;
        }
        $quantity = $this->valueParser->parseInteger($row['quantity'] ?? '');
        if (null !== $quantity) {
            // the stock command only expresses deltas: read the current
            // quantity and convert the file's absolute value (delta 0 is
            // illegal and means nothing to change)
            $delta = $quantity - $this->getCurrentStockQuantity($productId, $context->getShopId());
            if (0 !== $delta) {
                $command->setDeltaQuantity($delta);
                $hasUpdate = true;
            }
        }

        if ($hasUpdate) {
            $this->commandBus->handle($command);
        }

        return [];
    }

    /**
     * Current physical quantity of the product itself (no combination), 0
     * when no stock row exists yet. getForProduct() resolves shared-stock
     * setups (group-level stock rows) through the legacy shop restriction.
     */
    protected function getCurrentStockQuantity(int $productId, int $shopId): int
    {
        try {
            return (int) $this->stockAvailableRepository->getForProduct(new ProductId($productId), new ShopId($shopId))->quantity;
        } catch (StockAvailableNotFoundException) {
            return 0;
        }
    }
}
