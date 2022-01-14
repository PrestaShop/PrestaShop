<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetCombinationStockMovementHistory;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovementHistory;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierInfo;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides the data that is used to prefill the Combination form
 */
class CombinationFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var int
     */
    private $defaultShopId;

    /**
     * @var int|null
     */
    private $contextShopId;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param CommandBusInterface $queryBus
     * @param int $defaultShopId
     * @param int|null $contextShopId
     * @param TranslatorInterface $translator
     */
    public function __construct(
        CommandBusInterface $queryBus,
        int $defaultShopId,
        ?int $contextShopId,
        TranslatorInterface $translator
    ) {
        $this->queryBus = $queryBus;
        $this->defaultShopId = $defaultShopId;
        $this->contextShopId = $contextShopId;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function getData($id): array
    {
        $combinationId = (int) $id;
        $shopConstraint = ShopConstraint::shop($this->contextShopId ?? $this->defaultShopId);
        /** @var CombinationForEditing $combinationForEditing */
        $combinationForEditing = $this->queryBus->handle(new GetCombinationForEditing($combinationId));

        return [
            'id' => $combinationId,
            'product_id' => $combinationForEditing->getProductId(),
            'name' => $combinationForEditing->getName(),
            'stock' => $this->extractStockData($combinationForEditing, $shopConstraint),
            'price_impact' => $this->extractPriceImpactData($combinationForEditing),
            'references' => $this->extractReferencesData($combinationForEditing),
            'suppliers' => $this->extractSuppliersData($combinationForEditing),
            'images' => $combinationForEditing->getImageIds(),
        ];
    }

    /**
     * @param CombinationForEditing $combinationForEditing
     * @param ShopConstraint $shopConstraint
     *
     * @return array<string, mixed>
     */
    private function extractStockData(CombinationForEditing $combinationForEditing, ShopConstraint $shopConstraint): array
    {
        $stockInformation = $combinationForEditing->getStock();
        $availableDate = $stockInformation->getAvailableDate();

        return [
            'quantities' => [
                'delta_quantity' => [
                    'quantity' => $stockInformation->getQuantity(),
                    'delta' => 0,
                ],
                'stock_movement_history' => $this->getStockMovementHistories(
                    $combinationForEditing->getCombinationId(),
                    $shopConstraint
                ),
                'minimal_quantity' => $stockInformation->getMinimalQuantity(),
            ],
            'options' => [
                'stock_location' => $stockInformation->getLocation(),
                'low_stock_threshold' => $stockInformation->getLowStockThreshold() ?: null,
                'low_stock_alert' => $stockInformation->isLowStockAlertEnabled(),
            ],
            'available_date' => DateTime::isNull($availableDate) ? '' : $availableDate->format(DateTime::DEFAULT_DATE_FORMAT),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getStockMovementHistories(int $combinationId, ShopConstraint $shopConstraint): array
    {
        return array_map(
            function (StockMovementHistory $history): array {
                if ($history->isSingle()) {
                    $date = $history
                        ->getDate('add')
                        ->format(DateTime::DEFAULT_DATETIME_FORMAT)
                    ;
                    $employeeName = $this->translator->trans(
                        '%firstname% %lastname%',
                        [
                            '%firstname%' => $history->getEmployeeFirstname(),
                            '%lastname%' => $history->getEmployeeLastname(),
                        ],
                        'Modules.Customersignin.Admin'
                    );
                } elseif ($history->getDeltaQuantity() < 0) {
                    $date = $this->translator->trans('Shipped products');
                    $employeeName = null;
                } else {
                    $date = $this->translator->trans('Returned products');
                    $employeeName = null;
                }

                return [
                    'type' => $history->getType(),
                    'date' => $date,
                    'employee_name' => $employeeName,
                    'delta_quantity' => $history->getDeltaQuantity(),
                ];
            },
            $this->queryBus->handle(
                new GetCombinationStockMovementHistory(
                    $combinationId,
                    $shopConstraint->getShopId()->getValue()
                )
            )
        );
    }

    /**
     * @param CombinationForEditing $combinationForEditing
     *
     * @return array
     */
    private function extractPriceImpactData(CombinationForEditing $combinationForEditing): array
    {
        $priceImpactInformation = $combinationForEditing->getPrices();

        return [
            'wholesale_price' => (float) (string) $priceImpactInformation->getWholesalePrice(),
            'price_tax_excluded' => (float) (string) $priceImpactInformation->getImpactOnPrice(),
            'price_tax_included' => (float) (string) $priceImpactInformation->getImpactOnPriceTaxIncluded(),
            'ecotax' => (float) (string) $priceImpactInformation->getEcoTax(),
            'unit_price' => (float) (string) $priceImpactInformation->getImpactOnUnitPrice(),
            'weight' => (float) (string) $combinationForEditing->getDetails()->getImpactOnWeight(),
        ];
    }

    /**
     * @param CombinationForEditing $combinationForEditing
     *
     * @return array
     */
    private function extractReferencesData(CombinationForEditing $combinationForEditing): array
    {
        $details = $combinationForEditing->getDetails();

        return [
            'reference' => $details->getReference(),
            'isbn' => $details->getIsbn(),
            'ean_13' => $details->getEan13(),
            'upc' => $details->getUpc(),
            'mpn' => $details->getMpn(),
        ];
    }

    /**
     * @param CombinationForEditing $combinationForEditing
     *
     * @return array<string, int|array<int, int|array<string, string|int>>>
     */
    private function extractSuppliersData(CombinationForEditing $combinationForEditing): array
    {
        /** @var ProductSupplierOptions $productSupplierOptions */
        $productSupplierOptions = $this->queryBus->handle(new GetProductSupplierOptions($combinationForEditing->getProductId()));

        /** @var ProductSupplierInfo[] $combinationSupplierInfos */
        $combinationSupplierInfos = $this->queryBus->handle(new GetCombinationSuppliers($combinationForEditing->getCombinationId()));

        if (empty($combinationSupplierInfos)) {
            return [];
        }

        $defaultSupplierId = $productSupplierOptions->getDefaultSupplierId();
        $suppliersData = [
            'default_supplier_id' => $defaultSupplierId,
        ];

        foreach ($combinationSupplierInfos as $supplierOption) {
            $supplierForEditing = $supplierOption->getProductSupplierForEditing();
            $supplierId = $supplierOption->getSupplierId();

            $suppliersData['supplier_ids'][] = $supplierId;
            $suppliersData['product_suppliers'][$supplierId] = [
                'supplier_id' => $supplierId,
                'supplier_name' => $supplierOption->getSupplierName(),
                'product_supplier_id' => $supplierForEditing->getProductSupplierId(),
                'price_tax_excluded' => $supplierForEditing->getPriceTaxExcluded(),
                'reference' => $supplierForEditing->getReference(),
                'currency_id' => $supplierForEditing->getCurrencyId(),
                'combination_id' => $supplierForEditing->getCombinationId(),
            ];
        }

        return $suppliersData;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultData(): array
    {
        // Not supposed to happen, Combinations are created vie Generator

        return [];
    }
}
