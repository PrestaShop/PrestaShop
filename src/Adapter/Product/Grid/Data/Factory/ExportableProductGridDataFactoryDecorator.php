<?php

namespace PrestaShop\PrestaShop\Adapter\Product\Grid\Data\Factory;

use Link;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Returns grid data for export content.
 */
final class ExportableProductGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $productGridDataFactory;

    /**
     * @var Link
     */
    private $link;

    /**
     * @param GridDataFactoryInterface $productGridDataFactory
     * @param Link $link
     */
    public function __construct(
        GridDataFactoryInterface $productGridDataFactory,
        Link $link
    ) {
        $this->productGridDataFactory = $productGridDataFactory;
        $this->link = $link;
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
     * Applies modifications.
     *
     * @param array $products
     *
     * @return array
     */
    private function applyModification(array $products)
    {
        foreach ($products as $i => $product) {
            $products[$i]['image'] = $this->link->getImageLink($product['link_rewrite'], $product['id_image']);
        }

        return $products;
    }
}
