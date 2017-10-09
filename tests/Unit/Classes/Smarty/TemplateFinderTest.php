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

namespace Tests\Unit\classes\Smarty;

use TemplateFinder;
use PHPUnit\Framework\TestCase;

class TemplateFinderTest extends TestCase
{
    protected $templateFinder;

    protected function setUp()
    {
        $root = realpath(_PS_ROOT_DIR_).'/tests/resources/template-hierarchy/templates/';

        /* @var TemplateFinderCore */
        $this->templateFinder = new TemplateFinder(array($root), '.tpl');
    }

    protected function tearDown()
    {
        $this->templateFinder = null;
    }

    public function testTheTemplateFoundForACategoryPageWithId()
    {
        $template = $this->templateFinder->getTemplate('catalog/listing/product-list', 'category', 9, 'fr-FR');
        $this->assertEquals($template, 'catalog/listing/category-9.tpl');
    }

    public function testTheTemplateFoundForACategoryPageWithNoneExistingId()
    {
        $template = $this->templateFinder->getTemplate('catalog/listing/product-list', 'category', 8, 'fr-FR');
        $this->assertEquals($template, 'catalog/listing/category.tpl');
    }

    public function testTheTemplateFoundForANoneExistingCategory()
    {
        $template = $this->templateFinder->getTemplate('catalog/listing/product-list', 'category-test', 8, 'fr-FR');
        $this->assertEquals($template, 'catalog/listing/product-list.tpl');
    }

    public function testWrongTemplateFallback()
    {
        $template = $this->templateFinder->getTemplate('catalog/listing/srg-list', 'category', false, 'fr-FR');
        $this->assertEquals($template, 'catalog/listing/category.tpl');
    }

    public function testNoFoundTemplateThrowException()
    {
        $this->setExpectedException('\PrestaShopException');
        $template = $this->templateFinder->getTemplate('catalog/listing/my-custom-list', 'custom', null, 'fr-FR');
    }
}
