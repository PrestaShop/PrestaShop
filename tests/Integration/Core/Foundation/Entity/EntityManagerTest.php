<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Core\Foundation\Entity;

use Tests\TestCase\IntegrationTestCase;
use PrestaShop\PrestaShop\Core\ContainerBuilder;
use CMSRole;
use CMSRoleRepository;
use Db;
use PrestaShop\PrestaShop\Tests\Unit\ContextMocker;
use Product;

class EntityManagerTest extends IntegrationTestCase
{
    private $container;
    private $entityManager;

    /**
     * @var ContextMocker
     */
    protected $contextMocker;

    protected function setUp()
    {
        parent::setUp();
        $this->contextMocker = new ContextMocker();
        $this->contextMocker->mockContext();
        $containerBuilder = new ContainerBuilder();
        $this->container = $containerBuilder->build();
        $this->entityManager = $this->container->make('\\PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\EntityManager');
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->contextMocker->resetContext();
    }

    public function test_explicitly_defined_repository_is_found_by_entitymanager()
    {
        $this->assertInstanceOf(
            '\\PrestaShop\\PrestaShop\\Core\\CMS\\CMSRoleRepository',
            $this->entityManager->getRepository('CMSRole')
        );
    }

    public function test_find_implicitly_defined_repository()
    {
        $repository = $this->entityManager->getRepository('Product');
        $product = $repository->findOne(1);
        $this->assertInstanceOf('Product', $product);
        $this->assertEquals(1, $product->id);
    }

    public function test_save_dataMapper_style()
    {
        $repository = $this->entityManager->getRepository('CMSRole');

        $entity = new CMSRole;

        $name = "Yo CMS Role " . rand();

        $entity->name    = $name;
        $entity->id_cms = 6666;

        $this->entityManager->save($entity);

        $this->assertGreaterThan(0, $repository->findOneByName($name)->id);

        // Clean DB !
        $this->entityManager->delete($entity);
    }
}
