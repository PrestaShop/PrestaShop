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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Grid\Data\Factory;

use Currency;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository;
use Shop;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Decorates original grid data and returns modified prices for grid display as well as calculated price with taxes.
 */
class ProductGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var ShopRepository
     */
    protected $shopRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var GridDataFactoryInterface
     */
    private $productGridDataFactory;

    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var int
     */
    private $defaultCurrencyId;

    /**
     * @var TaxComputer
     */
    private $taxComputer;

    /**
     * @var int
     */
    private $countryId;

    /**
     * @var ProductImagePathFactory
     */
    private $productImagePathFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var bool
     */
    private $taxEnabled;

    /**
     * @var bool
     */
    private $isEcotaxEnabled;

    /**
     * @var int
     */
    private $ecoTaxGroupId;

    /**
     * Use as cache for shop names.
     *
     * @var array<int, Shop>
     */
    private $shopsNames;

    public function __construct(
        GridDataFactoryInterface $productGridDataFactory,
        Repository $localeRepository,
        string $contextLocale,
        int $defaultCurrencyId,
        TaxComputer $taxComputer,
        int $countryId,
        ProductImagePathFactory $productImagePathFactory,
        TranslatorInterface $translator,
        bool $taxEnabled,
        bool $isEcotaxEnabled,
        int $ecoTaxGroupId,
        ShopRepository $shopRepository,
        ProductRepository $productRepository
    ) {
        $this->productGridDataFactory = $productGridDataFactory;

        $this->locale = $localeRepository->getLocale(
            $contextLocale
        );

        $this->defaultCurrencyId = $defaultCurrencyId;
        $this->taxComputer = $taxComputer;
        $this->countryId = $countryId;
        $this->productImagePathFactory = $productImagePathFactory;
        $this->translator = $translator;
        $this->taxEnabled = $taxEnabled;
        $this->isEcotaxEnabled = $isEcotaxEnabled;
        $this->ecoTaxGroupId = $ecoTaxGroupId;
        $this->shopRepository = $shopRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        if (!$searchCriteria instanceof ShopSearchCriteriaInterface) {
            throw new InvalidArgumentException(sprintf('Invalid search criteria, expected a %s', ShopSearchCriteriaInterface::class));
        }

        $productData = $this->productGridDataFactory->getData($searchCriteria);
        $modifiedRecords = $this->applyModification($productData->getRecords()->all(), $searchCriteria);

        return new GridData(
            new RecordCollection($modifiedRecords),
            $productData->getRecordsTotal(),
            $productData->getQuery()
        );
    }

    /**
     * Applies modifications for product grid.
     *
     * @param array $products
     * @param ShopSearchCriteriaInterface $searchCriteria
     *
     * @return array
     */
    private function applyModification(array $products, ShopSearchCriteriaInterface $searchCriteria): array
    {
        $currency = new Currency($this->defaultCurrencyId);
        foreach ($products as $i => $product) {
            if (empty($product['name'])) {
                $products[$i]['name'] = $this->translator->trans('N/A', [], 'Admin.Global');
            }

            if ($product['id_image']) {
                $products[$i]['image'] = $this->productImagePathFactory->getPathByType(new ImageId((int) $product['id_image']), ProductImagePathFactory::IMAGE_TYPE_SMALL_DEFAULT);
            } else {
                $products[$i]['image'] = $this->productImagePathFactory->getNoImagePath(ProductImagePathFactory::IMAGE_TYPE_SMALL_DEFAULT, $this->getLanguageIsoCode());
            }
            // If no legend is defined use the name as a fallback (used for alt property on image)
            if (empty($product['legend'])) {
                $products[$i]['legend'] = $products[$i]['name'];
            }

            $productTaxRulesGroupId = new TaxRulesGroupId((int) ($products[$i]['id_tax_rules_group'] ?? 0));
            $priceTaxExcluded = new DecimalNumber((string) ($products[$i]['price_tax_excluded'] ?? 0));
            $ecotaxTaxExcluded = new DecimalNumber((string) ($products[$i]['ecotax_tax_excluded'] ?? 0));
            $countryId = new CountryId($this->countryId);

            if ($this->taxEnabled) {
                $priceTaxIncluded = $this->taxComputer->computePriceWithTaxes(
                    $priceTaxExcluded,
                    $productTaxRulesGroupId,
                    $countryId
                );
                $ecotaxTaxIncluded = $this->taxComputer->computePriceWithTaxes(
                    $ecotaxTaxExcluded,
                    new TaxRulesGroupId($this->ecoTaxGroupId),
                    $countryId
                );
            } else {
                $priceTaxIncluded = $priceTaxExcluded;
                $ecotaxTaxIncluded = $ecotaxTaxExcluded;
            }

            // Ecotax is applied independently of tax enabled
            if ($this->isEcotaxEnabled) {
                // We assume the list should display the final price tax excluded, not the actual value of the price tax excluded
                $priceTaxExcluded = $priceTaxExcluded->plus($ecotaxTaxExcluded);
                $priceTaxIncluded = $priceTaxIncluded->plus($ecotaxTaxIncluded);
            }

            $products[$i]['price_tax_excluded'] = $this->locale->formatPrice(
                (string) $priceTaxExcluded,
                $currency->iso_code
            );

            $products[$i]['price_tax_included'] = $this->locale->formatPrice(
                (string) $priceTaxIncluded,
                $currency->iso_code
            );
        }

        return $this->applyShopModifications($products, $searchCriteria);
    }

    protected function applyShopModifications(array $products, ShopSearchCriteriaInterface $searchCriteria): array
    {
        foreach ($products as $i => $product) {
            // Transform list of IDs into list of names
            if ($searchCriteria->getShopConstraint()->forAllShops() || null !== $searchCriteria->getShopConstraint()->getShopGroupId()) {
                $shopIds = $this->productRepository->getAssociatedShopIds(new ProductId((int) $product['id_product']));
                $products[$i]['associated_shops_ids'] = array_map(static function (ShopId $shopId) {
                    return $shopId->getValue();
                }, $shopIds);
                $products[$i]['associated_shops'] = $this->getShopsNames(
                    $shopIds,
                    $searchCriteria
                );
            }
        }

        return $products;
    }

    /**
     * Returns language iso code based on locale, en-US => en
     *
     * @return string
     */
    private function getLanguageIsoCode(): string
    {
        return explode('-', $this->locale->getCode())[0];
    }

    /**
     * @param ShopId[] $shopIds
     * @param ShopSearchCriteriaInterface $searchCriteria
     *
     * @return array
     */
    private function getShopsNames(array $shopIds, ShopSearchCriteriaInterface $searchCriteria): array
    {
        $shopNames = [];
        foreach ($shopIds as $shopId) {
            if (!isset($this->shopsNames[$shopId->getValue()])) {
                $this->shopsNames[$shopId->getValue()] = $this->shopRepository->get($shopId);
            }
            $shop = $this->shopsNames[$shopId->getValue()];

            // In shop group context we only display the shops from group
            if ($searchCriteria->getShopConstraint()->getShopGroupId() && $searchCriteria->getShopConstraint()->getShopGroupId()->getValue() !== (int) $shop->id_shop_group) {
                continue;
            }
            $shopNames[] = $shop->name;
        }

        return $shopNames;
    }
}
