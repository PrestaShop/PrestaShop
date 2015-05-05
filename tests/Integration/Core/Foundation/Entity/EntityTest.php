<?php

namespace PrestaShop\PrestaShop\Tests\Integration\Core\Foundation\Entity;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;

use Product;

class EntityTest extends IntegrationTestCase
{
	public function test_save_activeRecord_style()
	{
		$product = new Product(null, false, 1);
        $product->name = 'A Product';
        $product->price = 42.42;
        $product->link_rewrite = 'a-product';
		$this->assertTrue($product->save());
	}
}
