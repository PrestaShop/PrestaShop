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

use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetCombinationStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetAssociatedSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\AssociatedSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use PrestaShopBundle\Form\Extension\DisablingSwitchExtension;

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
     * @var Context
     */
    private $shopContext;

    /**
     * @param CommandBusInterface $queryBus
     * @param Context $shopContext
     */
    public function __construct(
        CommandBusInterface $queryBus,
        Context $shopContext
    ) {
        $this->queryBus = $queryBus;
        $this->shopContext = $shopContext;
    }

    /**
     * {@inheritDoc}
     */
    public function getData($id): array
    {
        $combinationId = (int) $id;
        $shopConstraint = $this->shopContext->getShopConstraint();
        /** @var CombinationForEditing $combinationForEditing */
        $combinationForEditing = $this->queryBus->handle(new GetCombinationForEditing(
            $combinationId,
            $shopConstraint
        ));

        $suppliersData = $this->extractSuppliersData($combinationForEditing);

        return array_merge([
            'id' => $combinationId,
            'product_id' => $combinationForEditing->getProductId(),
            'cover_thumbnail_url' => $combinationForEditing->getCoverThumbnailUrl(),
            'header' => [
                'name' => $combinationForEditing->getName(),
                'is_default' => $combinationForEditing->isDefault(),
            ],
            'stock' => $this->extractStockData($combinationForEditing, $shopConstraint),
            'price_impact' => $this->extractPriceImpactData($combinationForEditing),
            'references' => $this->extractReferencesData($combinationForEditing),
        ], $suppliersData, ['images' => $combinationForEditing->getImageIds()]);
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
                'stock_movements' => $this->getStockMovementHistories(
                    $combinationForEditing->getCombinationId(),
                    $shopConstraint
                ),
                'minimal_quantity' => $stockInformation->getMinimalQuantity(),
            ],
            'options' => [
                'stock_location' => $stockInformation->getLocation(),
                'low_stock_threshold' => $stockInformation->getLowStockThreshold(),
                sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX) => $stockInformation->isLowStockAlertEnabled(),
            ],
            'available_date' => DateTime::isNull($availableDate) ? '' : $availableDate->format(DateTime::DEFAULT_DATE_FORMAT),
            'available_now_label' => $stockInformation->getLocalizedAvailableNowLabels(),
            'available_later_label' => $stockInformation->getLocalizedAvailableLaterLabels(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getStockMovementHistories(int $combinationId, ShopConstraint $shopConstraint): array
    {
        return array_map(
            function (StockMovement $stockMovement): array {
                $date = null;
                if ($stockMovement->isEdition()) {
                    $date = $stockMovement
                        ->getDate('add')
                        ->format(DateTime::DEFAULT_DATETIME_FORMAT)
                    ;
                }

                return [
                    'type' => $stockMovement->getType(),
                    'date' => $date,
                    'employee_name' => $stockMovement->getEmployeeName(),
                    'delta_quantity' => $stockMovement->getDeltaQuantity(),
                ];
            },
            $this->queryBus->handle(
                new GetCombinationStockMovements(
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
            'price_tax_excluded' => (float) (string) $priceImpactInformation->getImpactOnPrice(),
            'price_tax_included' => (float) (string) $priceImpactInformation->getImpactOnPriceTaxIncluded(),
            'unit_price_tax_excluded' => (float) (string) $priceImpactInformation->getImpactOnUnitPrice(),
            'unit_price_tax_included' => (float) (string) $priceImpactInformation->getImpactOnUnitPriceTaxIncluded(),
            'ecotax_tax_excluded' => (float) (string) $priceImpactInformation->getEcotax(),
            'ecotax_tax_included' => (float) (string) $priceImpactInformation->getEcotaxTaxIncluded(),
            'wholesale_price' => (float) (string) $priceImpactInformation->getWholesalePrice(),
            'weight' => (float) (string) $combinationForEditing->getDetails()->getImpactOnWeight(),
            'product_tax_rate' => (float) (string) $priceImpactInformation->getProductTaxRate(),
            'product_price_tax_excluded' => (float) (string) $priceImpactInformation->getProductPrice(),
            'product_ecotax_tax_excluded' => (float) (string) $priceImpactInformation->getProductEcotax(),
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
     * @return array<string, array<int, array<string, int|string|null>>|int>
     */
    private function extractSuppliersData(CombinationForEditing $combinationForEditing): array
    {
        /** @var AssociatedSuppliers $associatedSuppliers */
        $associatedSuppliers = $this->queryBus->handle(new GetAssociatedSuppliers($combinationForEditing->getProductId()));
        $suppliersData = [
            'default_supplier_id' => $associatedSuppliers->getDefaultSupplierId(),
            'product_suppliers' => [],
        ];

        /** @var ProductSupplierForEditing[] $combinationProductSuppliers */
        $combinationProductSuppliers = $this->queryBus->handle(new GetCombinationSuppliers($combinationForEditing->getCombinationId()));

        if (empty($combinationProductSuppliers)) {
            return $suppliersData;
        }

        foreach ($combinationProductSuppliers as $supplierForEditing) {
            $supplierId = $supplierForEditing->getSupplierId();

            $suppliersData['product_suppliers'][$supplierId] = [
                'supplier_id' => $supplierId,
                'supplier_name' => $supplierForEditing->getSupplierName(),
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
        // Not supposed to happen, Combinations are created via Generator

        return [];
    }
}
