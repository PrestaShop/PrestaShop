<?php

namespace PrestaShop\PrestaShop\Tests\Integration\Core\Foundation\Entity;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;

use Core_Foundation_IoC_ContainerBuilder;

use CMSRoleEntity;
use CMSRoleRepository;
use Db;
use Product;

class EntityManagerTest extends IntegrationTestCase
{
	private $container;

	public function setup()
	{
		$this->container = (new Core_Foundation_IoC_ContainerBuilder)->build();
	}

	public function test_entitymanager_is_loaded_by_container()
	{
		$this->container->make('Core_Foundation_Database_EntityManager');
	}

	public function test_explicitly_defined_repository_is_found_by_entitymanager()
	{
		$em = $this->container->make('Core_Foundation_Database_EntityManager');
		$em->getRepository('CMSRoleEntity');
	}

	public function test_implicitly_defined_repository_is_found_by_entitymanager()
	{
		$em = $this->container->make('Core_Foundation_Database_EntityManager');
		$em->getRepository('Product');
	}

	public function test_find_implicitly_defined_repository()
	{
		$em = $this->container->make('Core_Foundation_Database_EntityManager');
		$repo = $em->getRepository('Product');
		$product = $repo->find(1);
		$this->assertInstanceOf('Product', $product);
		$this->assertEquals(1, $product->id);
	}
}
