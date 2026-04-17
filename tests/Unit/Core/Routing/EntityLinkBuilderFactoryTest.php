<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Routing;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Routing\EntityLinkBuilderFactory;
use PrestaShop\PrestaShop\Core\Routing\EntityLinkBuilderInterface;

class EntityLinkBuilderFactoryTest extends TestCase
{
    public function testConstructor()
    {
        $factory = new EntityLinkBuilderFactory([$this->getEntityLinkBuilderMock(['product'])]);
        $this->assertNotNull($factory);
    }

    public function testGetBuilder()
    {
        $builder1 = $this->getEntityLinkBuilderMock(['customer', 'product']);
        $builder2 = $this->getEntityLinkBuilderMock(['order', 'product']);

        $factory = new EntityLinkBuilderFactory([$builder1, $builder2]);
        $this->assertEquals($builder1, $factory->getBuilderFor('customer'));
        $this->assertEquals($builder2, $factory->getBuilderFor('order'));
        $this->assertEquals($builder1, $factory->getBuilderFor('product'));
    }

    /**
     * @param array $managedEntities
     *
     * @return MockObject|EntityLinkBuilderInterface
     */
    private function getEntityLinkBuilderMock(array $managedEntities)
    {
        $builderMock = $this->getMockBuilder(EntityLinkBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $builderMock
            ->method('canBuild')
            ->willReturnCallback(function ($entity) use ($managedEntities) {
                return in_array($entity, $managedEntities);
            })
        ;

        return $builderMock;
    }
}
