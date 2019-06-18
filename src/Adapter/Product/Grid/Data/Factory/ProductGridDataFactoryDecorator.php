<?php

namespace PrestaShop\PrestaShop\Adapter\Product\Grid\Data\Factory;

use Configuration;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;
use Product;

/**
 * Gets modified data for product grid.
 */
final class ProductGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $productGridDataFactory;

    /**
     * @var ImageProviderInterface
     */
    private $productImageProvider;

    /**
     * @param GridDataFactoryInterface $productGridDataFactory
     * @param ImageProviderInterface $productImageProvider
     */
    public function __construct(
        GridDataFactoryInterface $productGridDataFactory,
        ImageProviderInterface $productImageProvider
    ) {

        $this->productGridDataFactory = $productGridDataFactory;
        $this->productImageProvider = $productImageProvider;
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
        foreach ($products as $i => $product) {
            $products[$i]['image'] = $this->productImageProvider->getPath($product['id_image']);
            $products[$i]['price_tax_included'] = $this->getPriceWithTax($product);
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
