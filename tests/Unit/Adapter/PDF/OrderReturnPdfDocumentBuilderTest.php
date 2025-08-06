<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\PDF;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\PDF\Document\OrderReturnPdfDocumentBuilder;
use PrestaShop\PrestaShop\Adapter\PDF\PdfDocumentCommonDataBuilder;
use PrestaShop\PrestaShop\Adapter\PDF\PdfTemplateResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class OrderReturnPdfDocumentBuilderTest extends TestCase
{
    private OrderReturnPdfDocumentBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        // PdfTemplateResolver and PdfDocumentCommonDataBuilder are final and therefore
        // cannot be doubled: supports()/getBulkFilename() never use them, so real (but
        // otherwise inert) instances are enough here.
        $this->builder = new OrderReturnPdfDocumentBuilder(
            new PdfTemplateResolver($this->createMock(Environment::class), '/tmp'),
            new PdfDocumentCommonDataBuilder(),
            $this->createMock(TranslatorInterface::class)
        );
    }

    /**
     * @dataProvider supportsDataProvider
     */
    public function testSupports(string $type, bool $expected): void
    {
        $this->assertSame($expected, $this->builder->supports($type));
    }

    public function supportsDataProvider(): iterable
    {
        yield 'the order return template type is supported' => ['OrderReturn', true];
        yield 'the invoice template type is not supported' => ['Invoice', false];
        yield 'the order slip (credit slip) template type is not supported' => ['OrderSlip', false];
        yield 'an empty type is not supported' => ['', false];
    }

    public function testGetBulkFilenameMatchesTheLegacyHtmlTemplateOrderReturnValue(): void
    {
        // Legacy quirk kept as-is: HTMLTemplateOrderReturn::getBulkFilename() returns
        // 'invoices.pdf', not an order-return-specific bulk filename.
        $this->assertSame('invoices.pdf', $this->builder->getBulkFilename());
    }
}
