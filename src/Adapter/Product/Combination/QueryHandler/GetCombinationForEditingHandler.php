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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\QueryHandler;

use Combination;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler\GetCombinationForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationPrices;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationStock;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilderInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use Product;

/**
 * Handles @see GetCombinationForEditing query using legacy object model
 */
class GetCombinationForEditingHandler implements GetCombinationForEditingHandlerInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var CombinationNameBuilderInterface
     */
    private $combinationNameBuilder;

    /**
     * @var StockAvailableRepository
     */
    private $stockAvailableRepository;

    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @var NumberExtractor
     */
    private $numberExtractor;

    /**
     * @var TaxComputer
     */
    private $taxComputer;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param CombinationRepository $combinationRepository
     * @param CombinationNameBuilderInterface $combinationNameBuilder
     * @param StockAvailableRepository $stockAvailableRepository
     * @param AttributeRepository $attributeRepository
     * @param ProductRepository $productRepository
     * @param ProductImageRepository $productImageRepository
     * @param NumberExtractor $numberExtractor
     * @param TaxComputer $taxComputer
     * @param int $contextLanguageId
     * @param Configuration $configuration
     */
    public function __construct(
        CombinationRepository $combinationRepository,
        CombinationNameBuilderInterface $combinationNameBuilder,
        StockAvailableRepository $stockAvailableRepository,
        AttributeRepository $attributeRepository,
        ProductRepository $productRepository,
        ProductImageRepository $productImageRepository,
        NumberExtractor $numberExtractor,
        TaxComputer $taxComputer,
        int $contextLanguageId,
        Configuration $configuration
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->combinationNameBuilder = $combinationNameBuilder;
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->attributeRepository = $attributeRepository;
        $this->productRepository = $productRepository;
        $this->productImageRepository = $productImageRepository;
        $this->numberExtractor = $numberExtractor;
        $this->taxComputer = $taxComputer;
        $this->contextLanguageId = $contextLanguageId;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCombinationForEditing $query): CombinationForEditing
    {
        $combination = $this->combinationRepository->get($query->getCombinationId());
        $productId = new ProductId((int) $combination->id_product);
        $defaultCombinationId = $this->combinationRepository->getDefaultCombinationId($productId);
        $product = $this->productRepository->get($productId);

        return new CombinationForEditing(
            $query->getCombinationId()->getValue(),
            $productId->getValue(),
            $this->getCombinationName($query->getCombinationId()),
            $this->getDetails($combination),
            $this->getPrices($combination, $product),
            $this->getStock($combination),
            $this->getImages($combination),
            $defaultCombinationId && $defaultCombinationId->getValue() === (int) $combination->id
        );
    }

    /**
     * @param CombinationId $combinationId
     *
     * @return string
     */
    private function getCombinationName(CombinationId $combinationId): string
    {
        $attributesInformation = $this->attributeRepository->getAttributesInfoByCombinationIds(
            [$combinationId],
            new LanguageId($this->contextLanguageId)
        );

        return $this->combinationNameBuilder->buildName($attributesInformation[$combinationId->getValue()]);
    }

    /**
     * @param Combination $combination
     *
     * @return CombinationDetails
     */
    private function getDetails(Combination $combination): CombinationDetails
    {
        return new CombinationDetails(
            $combination->ean13,
            $combination->isbn,
            $combination->mpn,
            $combination->reference,
            $combination->upc,
            $this->numberExtractor->extract($combination, 'weight')
        );
    }

    /**
     * @param Combination $combination
     * @param Product $product
     *
     * @return CombinationPrices
     */
    private function getPrices(Combination $combination, Product $product): CombinationPrices
    {
        $taxEnabled = (bool) $this->configuration->get('PS_TAX', null, ShopConstraint::allShops());
        $ecoTaxGroupId = (int) $this->configuration->get('PS_ECOTAX_TAX_RULES_GROUP_ID', null, ShopConstraint::allShops());
        $defaultCountryId = (int) $this->configuration->get('PS_COUNTRY_DEFAULT', null, ShopConstraint::allShops());
        $defaultCountryId = new CountryId($defaultCountryId);

        $productTaxRulesGroupId = new TaxRulesGroupId((int) $product->id_tax_rules_group);

        $impactPriceTaxExcluded = $this->numberExtractor->extract($combination, 'price');
        $impactUnitPriceTaxExcluded = $this->numberExtractor->extract($combination, 'unit_price_impact');
        $ecotaxTaxExcluded = $this->numberExtractor->extract($combination, 'ecotax');

        if ($taxEnabled) {
            $impactPriceTaxIncluded = $this->taxComputer->computePriceWithTaxes(
                $impactPriceTaxExcluded,
                $productTaxRulesGroupId,
                $defaultCountryId
            );

            $impactUnitPriceTaxIncluded = $this->taxComputer->computePriceWithTaxes(
                $impactUnitPriceTaxExcluded,
                $productTaxRulesGroupId,
                $defaultCountryId
            );

            $ecotaxTaxIncluded = $this->taxComputer->computePriceWithTaxes(
                $ecotaxTaxExcluded,
                new TaxRulesGroupId($ecoTaxGroupId),
                $defaultCountryId
            );
            $productTaxRate = $this->taxComputer->getTaxRate($productTaxRulesGroupId, $defaultCountryId);
        } else {
            $impactPriceTaxIncluded = $impactPriceTaxExcluded;
            $impactUnitPriceTaxIncluded = $impactUnitPriceTaxExcluded;
            $ecotaxTaxIncluded = $ecotaxTaxExcluded;
            $productTaxRate = new DecimalNumber('0');
        }

        return new CombinationPrices(
            $impactPriceTaxExcluded,
            $impactPriceTaxIncluded,
            $impactUnitPriceTaxExcluded,
            $impactUnitPriceTaxIncluded,
            $ecotaxTaxExcluded,
            $ecotaxTaxIncluded,
            $this->numberExtractor->extract($combination, 'wholesale_price'),
            $productTaxRate,
            $this->numberExtractor->extract($product, 'price'),
            $this->numberExtractor->extract($product, 'ecotax')
        );
    }

    /**
     * @param Combination $combination
     *
     * @return CombinationStock
     */
    private function getStock(Combination $combination): CombinationStock
    {
        $stockAvailable = $this->stockAvailableRepository->getForCombination(new Combinationid($combination->id));

        return new CombinationStock(
            (int) $stockAvailable->quantity,
            (int) $combination->minimal_quantity,
            (int) $combination->low_stock_threshold,
            (bool) $combination->low_stock_alert,
            $stockAvailable->location,
            DateTimeUtil::buildDateTimeOrNull($combination->available_date)
        );
    }

    /**
     * @param Combination $combination
     *
     * @return int[]
     */
    private function getImages(Combination $combination): array
    {
        $combinationIdValue = (int) $combination->id;
        $combinationId = new CombinationId($combinationIdValue);
        $combinationImageIds = $this->productImageRepository->getImagesIdsForCombinations([$combinationId]);

        if (empty($combinationImageIds[$combinationIdValue])) {
            return [];
        }

        return array_map(function (ImageId $imageId) {
            return $imageId->getValue();
        }, $combinationImageIds[$combinationIdValue]);
    }
}
