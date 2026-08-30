<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\FeaturesChoiceProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\Query\GetCombinationFeatureValues;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\QueryResult\CombinationFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetCombinationStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetAssociatedSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\AssociatedSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
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
     * @var ShopContext
     */
    private $shopContext;

    /**
     * @var LanguageContext
     */
    private $languageContext;

    /**
     * @var FeaturesChoiceProvider
     */
    private $featuresChoiceProvider;

    /**
     * @var FeatureFlagStateCheckerInterface
     */
    private $featureFlagStateChecker;

    /**
     * @var array<int, string>|null
     */
    private $featureNames = null;

    /**
     * @param CommandBusInterface $queryBus
     * @param ShopContext $shopContext
     * @param LanguageContext $languageContext
     * @param FeaturesChoiceProvider $featuresChoiceProvider
     * @param FeatureFlagStateCheckerInterface $featureFlagStateChecker
     */
    public function __construct(
        CommandBusInterface $queryBus,
        ShopContext $shopContext,
        LanguageContext $languageContext,
        FeaturesChoiceProvider $featuresChoiceProvider,
        FeatureFlagStateCheckerInterface $featureFlagStateChecker
    ) {
        $this->queryBus = $queryBus;
        $this->shopContext = $shopContext;
        $this->languageContext = $languageContext;
        $this->featuresChoiceProvider = $featuresChoiceProvider;
        $this->featureFlagStateChecker = $featureFlagStateChecker;
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

        $data = array_merge([
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

        // The feature values section only exists when the feature flag is enabled (so does the matching
        // form type), hence the data is only populated in that case to avoid feeding an absent field.
        if ($this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_COMBINATION_FEATURE_VALUES)) {
            $data['features'] = $this->extractFeatureValues($combinationId, $shopConstraint);
        }

        return $data;
    }

    /**
     * @param int $combinationId
     * @param ShopConstraint $shopConstraint
     *
     * @return array
     */
    private function extractFeatureValues(int $combinationId, ShopConstraint $shopConstraint): array
    {
        /** @var CombinationFeatureValue[] $featureValues */
        $featureValues = $this->queryBus->handle(new GetCombinationFeatureValues($combinationId, $shopConstraint->getShopId()->getValue()));
        if (empty($featureValues)) {
            return [];
        }

        $featureNames = $this->getFeatureNames();
        $combinationFeatureCollection = [];
        foreach ($featureValues as $featureValue) {
            if (!isset($combinationFeatureCollection[$featureValue->getFeatureId()])) {
                $combinationFeatureCollection[$featureValue->getFeatureId()] = [
                    'feature_id' => $featureValue->getFeatureId(),
                    'feature_name' => $featureNames[$featureValue->getFeatureId()] ?? '',
                    'feature_values' => [],
                ];
            }

            $combinationFeatureValue = [
                'feature_value_id' => $featureValue->getFeatureValueId(),
                'feature_value_name' => $featureValue->getLocalizedValues()[$this->languageContext->getId()] ?? '',
                'is_custom' => $featureValue->isCustom(),
            ];
            if ($featureValue->isCustom()) {
                $combinationFeatureValue['custom_value'] = $featureValue->getLocalizedValues();
            }

            $combinationFeatureCollection[$featureValue->getFeatureId()]['feature_values'][] = $combinationFeatureValue;
        }

        return [
            // Return 0-indexed array, not mapped by feature ID
            'feature_collection' => array_values($combinationFeatureCollection),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function getFeatureNames(): array
    {
        if (null === $this->featureNames) {
            $this->featureNames = [];
            $featureChoices = $this->featuresChoiceProvider->getChoices();
            foreach ($featureChoices as $featureName => $featureId) {
                $this->featureNames[$featureId] = $featureName;
            }
        }

        return $this->featureNames;
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
                    'api_client_name' => implode(', ', $stockMovement->getApiClientNames()) ?: null,
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
            'ean_13' => $details->getGtin(),
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
