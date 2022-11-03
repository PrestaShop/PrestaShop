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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
