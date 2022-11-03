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

declare(strict_types=1);

namespace Tests\Unit\Classes\Smarty;

use PHPUnit\Framework\TestCase;
use TemplateFinder;

class TemplateFinderTest extends TestCase
{
    /**
     * @var TemplateFinder|null
     */
    protected $templateFinder;

    protected function setUp(): void
    {
        $root = realpath(_PS_ROOT_DIR_) . '/tests/Resources/template-hierarchy/templates/';

        /* @var TemplateFinder */
        $this->templateFinder = new TemplateFinder([$root], '.tpl');
    }

    protected function tearDown(): void
    {
        $this->templateFinder = null;
    }

    public function testTheTemplateFoundForACategoryPageWithId(): void
    {
        $template = $this->templateFinder->getTemplate('catalog/listing/product-list', 'category', 9, 'fr-FR');
        $this->assertEquals($template, 'catalog/listing/category-9.tpl');
    }

    public function testTheTemplateFoundForACategoryPageWithNoneExistingId(): void
    {
        $template = $this->templateFinder->getTemplate('catalog/listing/product-list', 'category', 8, 'fr-FR');
        $this->assertEquals($template, 'catalog/listing/category.tpl');
    }

    public function testTheTemplateFoundForANoneExistingCategory(): void
    {
        $template = $this->templateFinder->getTemplate('catalog/listing/product-list', 'category-test', 8, 'fr-FR');
        $this->assertEquals($template, 'catalog/listing/product-list.tpl');
    }

    public function testWrongTemplateFallback(): void
    {
        $template = $this->templateFinder->getTemplate('catalog/listing/srg-list', 'category', false, 'fr-FR');
        $this->assertEquals($template, 'catalog/listing/category.tpl');
    }

    public function testNoFoundTemplateThrowException(): void
    {
        $this->expectException('\PrestaShopException');
        $template = $this->templateFinder->getTemplate('catalog/listing/my-custom-list', 'custom', null, 'fr-FR');
    }
}
