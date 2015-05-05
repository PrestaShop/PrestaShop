<?php

namespace PrestaShop\PrestaShop\Tests\Integration\Core\Foundation\Entity;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;

use Core_Foundation_IoC_ContainerBuilder;

use CMSRole;
use CMSRoleRepository;
use Db;
use Product;

class EntityManagerTest extends IntegrationTestCase
{
	private $container;
	private $entityManager;

	public function setup()
	{
		$this->container = (new Core_Foundation_IoC_ContainerBuilder)->build();
		$this->entityManager = $this->container->make('Core_Foundation_Database_EntityManager');
	}

	public function test_find_implicitly_defined_repository()
	{
		$repository = $this->entityManager->getRepository('Product');
		$product = $repository->find(1);
		$this->assertInstanceOf('Product', $product);
		$this->assertEquals(1, $product->id);
	}

	public function test_save_dataMapper_style()
	{
		$repository = $this->entityManager->getRepository('CMSRole');

		$entity = new CMSRole;

		$name = "Yo CMS Role " . rand();

		$entity->name 	= $name;
		$entity->id_cms = 6666;

		$this->entityManager->save($entity);


		$this->assertGreaterThan(0, $repository->findOneByName($name)->id);
	}
}
