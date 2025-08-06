<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\PDF;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\PDF\Document\OrderSlipPdfDocumentBuilder;
use PrestaShop\PrestaShop\Adapter\PDF\PdfDocumentCommonDataBuilder;
use PrestaShop\PrestaShop\Adapter\PDF\PdfTemplateResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class OrderSlipPdfDocumentBuilderTest extends TestCase
{
    private OrderSlipPdfDocumentBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $templateResolver = new PdfTemplateResolver(
            $this->createMock(Environment::class),
            '/tmp'
        );

        $this->builder = new OrderSlipPdfDocumentBuilder(
            $templateResolver,
            new PdfDocumentCommonDataBuilder(),
            $this->createMock(TranslatorInterface::class)
        );
    }

    public function testSupportsOnlyTheOrderSlipTemplateType(): void
    {
        $this->assertTrue($this->builder->supports('OrderSlip'));
        $this->assertFalse($this->builder->supports('Invoice'));
        $this->assertFalse($this->builder->supports(''));
    }

    public function testGetBulkFilename(): void
    {
        $this->assertSame('order-slips.pdf', $this->builder->getBulkFilename());
    }
}
