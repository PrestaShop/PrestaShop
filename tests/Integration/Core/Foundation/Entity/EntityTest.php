<?php

namespace PrestaShop\PrestaShop\Tests\Integration\Core\Foundation\Entity;


use Db;
use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;

use CMSRoleEntity;
use CMSRoleRepository;
use Product;

class EntityTest extends IntegrationTestCase
{

	public function test_save_product()
	{
		$product = new Product(null, false, 1);
        $product->name = 'A Product';
        $product->price = 42.42;
        $product->link_rewrite = 'a-product';
		$this->assertTrue($product->save());
	}

	public function test_save()
	{

		$db = Db::getInstance();

		$repository = new CMSRoleRepository($db, _DB_PREFIX_);
		$entity = new CMSRoleEntity;

		$name = "Yo CMS Role " . rand();

		$entity->name = $name;
		$entity->id_cms = 6666;

		$repository->save($entity);


		$this->assertGreaterThan(0, $repository->findOneByName($name)->id);
	}
}
