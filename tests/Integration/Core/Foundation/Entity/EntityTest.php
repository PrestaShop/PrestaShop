<?php

namespace PrestaShop\PrestaShop\Tests\Integration\Core\Foundation\Entity;


use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;

use Adapter_Database;

use CMSRoleEntity;
use CMSRoleRepository;
use Db;
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

	public function test_save_dataMapper_style()
	{

		$repository = new CMSRoleRepository(new Adapter_Database, _DB_PREFIX_, 'CMSRoleEntity');
		$entity = new CMSRoleEntity;

		$name = "Yo CMS Role " . rand();

		$entity->name = $name;
		$entity->id_cms = 6666;

		$repository->save($entity);


		$this->assertGreaterThan(0, $repository->findOneByName($name)->id);
	}
}
