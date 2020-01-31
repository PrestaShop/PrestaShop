<?php

namespace PrestaShop\PrestaShop\Adapter\Product\Grid\Data\Factory;

use Configuration;
use Currency;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository;
use Product;

/**
 * Decorates original grid data and returns modified prices for grid display as well as calculated price with taxes.
 */
final class ProductPriceGridDataFactoryDecorator implements GridDataFactoryInterface
{
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
     * @param GridDataFactoryInterface $productGridDataFactory
     * @param Repository $localeRepository
     * @param string $contextLocale
     * @param int $defaultCurrencyId
     */
    public function __construct(
        GridDataFactoryInterface $productGridDataFactory,
        Repository $localeRepository,
        $contextLocale,
        $defaultCurrencyId
    ) {
        $this->productGridDataFactory = $productGridDataFactory;

        $this->locale = $localeRepository->getLocale(
            $contextLocale
        );

        $this->defaultCurrencyId = $defaultCurrencyId;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $productData = $this->productGridDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $productData->getRecords()->all()
        );

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
     *
     * @return array
     */
    private function applyModification(array $products)
    {
        $currency = new Currency($this->defaultCurrencyId);

        foreach ($products as $i => $product) {

            $products[$i]['price_tax_excluded'] = $this->locale->formatPrice(
                $products[$i]['price_tax_excluded'],
                $currency->iso_code
            );

            $products[$i]['price_tax_included'] = $this->locale->formatPrice(
                $this->getPriceWithTax($product),
                $currency->iso_code
            );
        }

        return $products;
    }

    /**
     * Gets price with tax.
     *
     * @param array $product
     *
     * @return float
     */
    private function getPriceWithTax(array $product)
    {
        return Product::getPriceStatic(
            $product['id_product'],
            true,
            null,
            (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION'),
            null,
            false,
            true,
            1,
            true,
            null,
            null,
            null,
            $nothing,
            true,
            true
        );
    }
}
