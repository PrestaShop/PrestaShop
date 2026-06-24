<?php
declare(strict_types=1);
namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;
use ProductDownload;

class ProductDownloadDefinitionTest extends TestCase
{
    public function testHasCombinationField(): void
    {
        $this->assertArrayHasKey('id_product_attribute', ProductDownload::$definition['fields']);
        $pd = new ProductDownload();
        $this->assertSame(0, (int) $pd->id_product_attribute);
        $this->assertTrue(method_exists(ProductDownload::class, 'getIdFromCombination'));
    }
}
