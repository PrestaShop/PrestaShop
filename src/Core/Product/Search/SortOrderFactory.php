<?php

namespace PrestaShop\PrestaShop\Core\Product\Search;

use PrestaShop\PrestaShop\Adapter\Translator;

class SortOrderFactory
{
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getDefaultSortOrders()
    {
        return [
            (new SortOrder('product', 'position', 'asc'))->setLabel(
                $this->translator->trans('Relevance', array(), 'Shop-Theme-Catalog')
            ),
            (new SortOrder('product', 'name', 'asc'))->setLabel(
                $this->translator->trans('Name, A to Z', array(), 'Shop-Theme-Catalog')
            ),
            (new SortOrder('product', 'name', 'desc'))->setLabel(
                $this->translator->trans('Name, Z to A', array(), 'Shop-Theme-Catalog')
            ),
            (new SortOrder('product', 'price', 'asc'))->setLabel(
                $this->translator->trans('Price, low to high', array(), 'Shop-Theme-Catalog')
            ),
            (new SortOrder('product', 'price', 'desc'))->setLabel(
                $this->translator->trans('Price, high to low', array(), 'Shop-Theme-Catalog')
            )
        ];
    }
}
