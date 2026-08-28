<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductDefaultSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\SupplierFinder;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Associates the row's supplier with the product, MERGING with the existing
 * associations: the file expresses one supplier and possibly a reference and
 * a wholesale price, but the Set/Update commands replace whole lists, so the
 * current state is read first and re-sent for everything the file says
 * nothing about (re-importing must never drop or blank supplier data).
 */
class SuppliersStep extends AbstractProductRowStep
{
    public function __construct(
        ValueParser $valueParser,
        protected readonly SupplierFinder $supplierFinder,
        protected readonly ProductSupplierRepository $productSupplierRepository,
        protected readonly ShopConfigurationInterface $configuration,
        protected readonly CommandBusInterface $commandBus,
        protected readonly TranslatorInterface $translator,
    ) {
        parent::__construct($valueParser);
    }

    public function supports(array $row): bool
    {
        return $this->hasValue($row, 'supplier');
    }

    public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array
    {
        $supplier = $row['supplier'] ?? '';

        $lookup = $this->supplierFinder->find($supplier, $context);
        $supplierId = $lookup->first();
        if (null === $supplierId) {
            // suppliers are never auto-created: a supplier requires an address,
            // which the import file cannot provide -> warn and drop
            return [new ImportMessage(
                ImportMessage::SEVERITY_WARNING,
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->translator->trans('Supplier "%name%" does not exist and suppliers are not auto-created by the import; the field will be ignored.', ['%name%' => $supplier], 'Admin.Advparameters.Notification'),
                [$rowIndex],
                'supplier'
            )];
        }

        $messages = [];
        if ($lookup->isAmbiguous()) {
            $messages[] = new ImportMessage(
                ImportMessage::SEVERITY_WARNING,
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->translator->trans('Supplier "%name%" matches %count% suppliers; the first one (id %id%) was used.', ['%name%' => $supplier, '%count%' => $lookup->count(), '%id%' => $supplierId], 'Admin.Advparameters.Notification'),
                [$rowIndex],
                'supplier'
            );
        }

        // the file expresses ONE supplier, but SetSuppliersCommand replaces the
        // whole list (associateSuppliers() bulk-deletes what is missing), so the
        // row's supplier is UNIONED with the existing ones: re-importing a
        // product must never drop the suppliers the file says nothing about
        $currentAssociations = $this->getCurrentProductSuppliers($productId);
        $supplierIds = array_keys($currentAssociations);
        if (!in_array($supplierId, $supplierIds, true)) {
            $supplierIds[] = $supplierId;
        }
        $this->commandBus->handle(new SetSuppliersCommand($productId, array_values($supplierIds)));

        // same reasoning per FIELD: UpdateProductSuppliersCommand replaces the
        // association, so an unmapped/empty cell must re-send the CURRENT value
        // instead of blanking it (legacy read them off the loaded product, whose
        // fillInfo() skipped empty cells)
        $existing = $currentAssociations[$supplierId] ?? null;
        $this->commandBus->handle(new UpdateProductSuppliersCommand($productId, [
            [
                'supplier_id' => $supplierId,
                // the import file has no currency column, so an EXISTING
                // association keeps the currency it was recorded with: resetting
                // it to the shop default would reinterpret the price without
                // changing the number (100 USD silently read as 100 EUR).
                // Legacy did reset it, so this is a deliberate divergence
                'currency_id' => null !== $existing
                    ? (int) $existing['id_currency']
                    : (int) $this->configuration->get('PS_CURRENCY_DEFAULT', null, $context->getShopConstraint()),
                'reference' => $this->hasValue($row, 'supplier_reference')
                    ? $row['supplier_reference']
                    : (string) ($existing['product_supplier_reference'] ?? ''),
                'price_tax_excluded' => $this->hasValue($row, 'wholesale_price')
                    ? (string) ($this->valueParser->parseDecimal($row['wholesale_price']) ?? new DecimalNumber('0'))
                    : (string) ($existing['product_supplier_price_te'] ?? '0'),
            ],
        ]));
        $this->commandBus->handle(new SetProductDefaultSupplierCommand($productId, $supplierId));

        return $messages;
    }

    /**
     * The product's current product-level supplier associations (no combination),
     * keyed by supplier id. Plain rows, never the legacy ObjectModel: importers
     * live in Core (getByAssociation() would hand back a ProductSupplier).
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getCurrentProductSuppliers(int $productId): array
    {
        $associations = [];
        foreach ($this->productSupplierRepository->getProductSuppliersInfo(new ProductId($productId), new NoCombinationId()) as $association) {
            $associations[(int) $association['id_supplier']] = $association;
        }

        return $associations;
    }
}
