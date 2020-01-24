<?php

namespace Tests\Unit\Core\Domain\Product\QueryHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductExportableData;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\GetProductExportableDataHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductExportableData;

class GetProductExportableDataHandlerTest extends TestCase
{
    public function testItGetsExportHeadersAndRowsInRightOrder()
    {
        $exportableProductData = new GetProductExportableDataHandler();

        $exportableData = new GetProductExportableData(
            [
                [
                    'id' => 'id_product',
                    'name' => 'Product Id',
                ],
                [
                    'id' => 'price',
                    'name' => 'Price',
                ],
                [
                    'id' => 'quantity',
                    'name' => 'Quantity',
                ],
                [
                    'id' => 'name',
                    'name' => 'Name',
                ],
                [
                    'id' => 'reference',
                    'name' => 'Reference',
                ],
                [
                    'id' => 'actions',
                    'name' => 'Actions',
                ],
            ],
            [
                [
                    'id_product' => 1,
                    'name' => 'test1',
                    'price' => 50.5,
                    'active' => true,
                ],
                [
                    'price' => 100,
                    'id_product' => 2,
                    'quantity' => 200,
                    'name' => 'test2',
                    'active' => false,
                ],
            ]
        );

        $result = $exportableProductData->handle($exportableData);

        $this->assertEquals(
            new ProductExportableData(
                [
                    'id_product' => 'Product Id',
                    'price' => 'Price',
                    'quantity' => 'Quantity',
                    'name' => 'Name',
                    'reference' => 'Reference',
                ],
                [
                    [
                        0 => 1,
                        1 => 50.5,
                        3 => 'test1',
                    ],
                    [
                        0 => 2,
                        1 => 100,
                        2 => 200,
                        3 => 'test2',
                    ],
                ]
            ),
            $result
        );
    }
}
