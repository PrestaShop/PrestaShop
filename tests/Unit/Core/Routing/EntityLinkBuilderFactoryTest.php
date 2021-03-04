<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
