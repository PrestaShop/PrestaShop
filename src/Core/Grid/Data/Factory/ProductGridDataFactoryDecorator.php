<?php

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;

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

    private function applyModification(array $products)
    {
        foreach ($products as $i => $product) {
            $products[$i]['image'] = $this->productImageProvider->getPath($product['id_image']);
        }

        return $products;
    }
}
