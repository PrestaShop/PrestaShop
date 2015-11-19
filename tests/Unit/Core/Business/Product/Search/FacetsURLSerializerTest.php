<?php

namespace PrestaShop\PrestaShop\tests\Unit\Core\Business\Product\Search;

use PHPUnit_Framework_Testcase;
use PrestaShop\PrestaShop\Core\Business\Product\Search\Facet;
use PrestaShop\PrestaShop\Core\Business\Product\Search\Filter;
use PrestaShop\PrestaShop\Core\Business\Product\Search\FacetsURLSerializer;

class FacetsURLSerializerTest extends PHPUnit_Framework_Testcase
{
    public function test_serialize_one_facet()
    {
        $facet = (new Facet)
            ->setLabel('Categories')
            ->addFilter((new Filter)->setLabel('Tops')->setActive(true))
            ->addFilter((new Filter)->setLabel('Robes')->setActive(true))
        ;

        $this->assertEquals('Categories-Tops-Robes', (new FacetsURLSerializer)->serialize([$facet]));
    }
}
