<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Adapter\Routing;

use Link;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Routing\AdminLinkBuilder;

class AdminLinkBuilderTest extends TestCase
{
    public function testConstructor()
    {
        $builder = new AdminLinkBuilder($this->getLinkMock(), [
            'customer' => 'AdminCustomer',
        ]);
        $this->assertNotNull($builder);
    }

    public function testCanBuild()
    {
        $builder = new AdminLinkBuilder($this->getLinkMock(), ['customer' => 'AdminCustomers']);

        $this->assertTrue($builder->canBuild('customer'));
        $this->assertFalse($builder->canBuild('product'));

        $builder = new AdminLinkBuilder($this->getLinkMock(), ['product' => 'AdminProducts']);

        $this->assertFalse($builder->canBuild('customer'));
        $this->assertTrue($builder->canBuild('product'));
    }

    public function testBuildViewLink()
    {
        $builder = new AdminLinkBuilder($this->getLinkMock(), ['customer' => 'AdminCustomers']);
        $viewLink = $builder->getViewLink('customer', ['id_customer' => 42]);
        $this->assertEquals('?controller=AdminCustomers&id_customer=42&viewcustomer=1', $viewLink);

        $viewLink = $builder->getViewLink('customer', ['id_customer' => 42, 'preview' => 'full']);
        $this->assertEquals('?controller=AdminCustomers&id_customer=42&preview=full&viewcustomer=1', $viewLink);

        $builder = new AdminLinkBuilder($this->getLinkMock(), ['product' => 'AdminProducts']);
        $viewLink = $builder->getViewLink('product', ['id_product' => 42]);
        $this->assertEquals('?controller=AdminProducts&id_product=42&viewproduct=1', $viewLink);

        $viewLink = $builder->getViewLink('product', ['id_product' => 42, 'preview' => 'full']);
        $this->assertEquals('?controller=AdminProducts&id_product=42&preview=full&viewproduct=1', $viewLink);
    }

    public function testBuildEditLink()
    {
        $builder = new AdminLinkBuilder($this->getLinkMock(), ['customer' => 'AdminCustomers']);
        $editLink = $builder->getEditLink('customer', ['id_customer' => 42]);
        $this->assertEquals('?controller=AdminCustomers&id_customer=42&updatecustomer=1', $editLink);

        $editLink = $builder->getEditLink('customer', ['id_customer' => 42, 'preview' => 'full']);
        $this->assertEquals('?controller=AdminCustomers&id_customer=42&preview=full&updatecustomer=1', $editLink);

        $builder = new AdminLinkBuilder($this->getLinkMock(), ['product' => 'AdminProducts']);
        $editLink = $builder->getEditLink('product', ['id_product' => 42]);
        $this->assertEquals('?controller=AdminProducts&id_product=42&updateproduct=1', $editLink);

        $editLink = $builder->getViewLink('product', ['id_product' => 42, 'preview' => 'full']);
        $this->assertEquals('?controller=AdminProducts&id_product=42&preview=full&viewproduct=1', $editLink);
    }

    public function testCleanTokenInLink()
    {
        $builder = new AdminLinkBuilder($this->getLinkMock(), ['product' => 'AdminProducts']);
        $editLink = $builder->getEditLink('product', ['id_product' => 42, 'token' => 'toto']);
        $this->assertEquals('?controller=AdminProducts&id_product=42&updateproduct=1', $editLink);
    }

    public function testCleanCurrentIndex()
    {
        $builder = new AdminLinkBuilder($this->getLinkMock(), ['product' => 'AdminProducts']);
        $editLink = $builder->getEditLink('product', ['id_product' => 42, 'current_index' => '/admin-dev/index.php/sell/customers/?_token=mYY9DFadRSfPTsJR-XXHHMQl_MXOCTZQ8n2bVlbeUMA']);
        $this->assertEquals('?controller=AdminProducts&id_product=42&updateproduct=1', $editLink);
    }

    /**
     * @return MockObject|Link
     */
    private function getLinkMock()
    {
        $linkMock = $this->getMockBuilder(Link::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $linkMock
            ->method('getAdminLink')
            ->willReturnCallback(function ($controller, $withToken, $sfRouteParams, $params) {
                $this->assertTrue($withToken);

                return '?controller=' . $controller . '&' . http_build_query(array_merge($sfRouteParams, $params));
            })
        ;

        return $linkMock;
    }
}
