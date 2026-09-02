<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
