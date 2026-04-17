<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Adapter\Routing;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Routing\LegacyHelperLinkBuilder;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class LegacyHelperLinkBuilderTest extends TestCase
{
    public function testBuildViewLink()
    {
        $builder = new LegacyHelperLinkBuilder();
        $viewLink = $builder->getViewLink('product', ['id_product' => 42, 'current_index' => 'index.php?controller=AdminProducts']);
        $this->assertEquals('index.php?controller=AdminProducts&viewproduct=&id_product=42', $viewLink);

        $viewLink = $builder->getViewLink('product', ['id_product' => 42, 'current_index' => 'index.php?controller=AdminProducts', 'token' => 'toto']);
        $this->assertEquals('index.php?controller=AdminProducts&viewproduct=&id_product=42&token=toto', $viewLink);

        $viewLink = $builder->getViewLink('product', ['id_product' => 42, 'current_index' => 'index.php?controller=AdminProducts', 'viewproduct' => 'on']);
        $this->assertEquals('index.php?controller=AdminProducts&viewproduct=on&id_product=42', $viewLink);
    }

    public function testBuildEditLink()
    {
        $builder = new LegacyHelperLinkBuilder();
        $editLink = $builder->getEditLink('product', ['id_product' => 42, 'current_index' => 'index.php?controller=AdminProducts']);
        $this->assertEquals('index.php?controller=AdminProducts&updateproduct=&id_product=42', $editLink);

        $editLink = $builder->getEditLink('product', ['id_product' => 42, 'current_index' => 'index.php?controller=AdminProducts', 'token' => 'toto']);
        $this->assertEquals('index.php?controller=AdminProducts&updateproduct=&id_product=42&token=toto', $editLink);

        $viewLink = $builder->getEditLink('product', ['id_product' => 42, 'current_index' => 'index.php?controller=AdminProducts', 'updateproduct' => 'enabled']);
        $this->assertEquals('index.php?controller=AdminProducts&updateproduct=enabled&id_product=42', $viewLink);
    }

    public function testViewLinkWithoutCurrentLinkFails()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing parameter current_index to build legacy link');

        $builder = new LegacyHelperLinkBuilder();
        $builder->getViewLink('product', ['id_product' => 42]);
    }

    public function testEditLinkWithoutCurrentLinkFails()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing parameter current_index to build legacy link');

        $builder = new LegacyHelperLinkBuilder();
        $builder->getEditLink('product', ['id_product' => 42]);
    }
}
