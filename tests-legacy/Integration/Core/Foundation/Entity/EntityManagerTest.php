<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Integration\Core\Foundation\Entity;

use CMSRole;
use Db;
use LegacyTests\TestCase\IntegrationTestCase;
use LegacyTests\Unit\ContextMocker;
use PrestaShop\PrestaShop\Core\ContainerBuilder;

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

    public function testExplicitlyDefinedRepositoryIsFoundByEntitymanager()
    {
        $this->assertInstanceOf(
            '\\PrestaShop\\PrestaShop\\Core\\CMS\\CMSRoleRepository',
            $this->entityManager->getRepository('CMSRole')
        );
    }

    public function testFindImplicitlyDefinedRepository()
    {
        $repository = $this->entityManager->getRepository('Product');
        $product = $repository->findOne(1);
        $this->assertInstanceOf('Product', $product);
        $this->assertEquals(1, $product->id);
    }

    public function testSaveDataMapperStyle()
    {
        $repository = $this->entityManager->getRepository('CMSRole');

        $entity = new CMSRole();

        $name = "Yo CMS Role " . mt_rand(0, mt_getrandmax());

        $entity->name    = $name;
        $entity->id_cms = 6666;

        $this->entityManager->save($entity);

        $this->assertGreaterThan(0, $repository->findOneByName($name)->id);

        // Clean DB !
        $this->entityManager->delete($entity);
    }
}
