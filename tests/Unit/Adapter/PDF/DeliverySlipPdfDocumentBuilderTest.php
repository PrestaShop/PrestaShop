<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\PDF;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\PDF\Document\DeliverySlipPdfDocumentBuilder;
use PrestaShop\PrestaShop\Adapter\PDF\PdfDocumentCommonDataBuilder;
use PrestaShop\PrestaShop\Adapter\PDF\PdfTemplateResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class DeliverySlipPdfDocumentBuilderTest extends TestCase
{
    private DeliverySlipPdfDocumentBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        // PdfTemplateResolver and PdfDocumentCommonDataBuilder are final and cannot be doubled;
        // supports()/getBulkFilename() never call into them, so plain real instances are enough here.
        $this->builder = new DeliverySlipPdfDocumentBuilder(
            new PdfTemplateResolver(new Environment(new ArrayLoader()), '/tmp'),
            new PdfDocumentCommonDataBuilder(),
            $this->createMock(TranslatorInterface::class)
        );
    }

    /**
     * @dataProvider provideSupportsCases
     */
    public function testSupports(string $type, bool $expected): void
    {
        $this->assertSame($expected, $this->builder->supports($type));
    }

    public static function provideSupportsCases(): iterable
    {
        yield 'delivery slip type is supported' => ['DeliverySlip', true];
        yield 'invoice type is not supported' => ['Invoice', false];
        yield 'shipment delivery slip type is not supported' => ['ShipmentDeliverySlip', false];
        yield 'empty string is not supported' => ['', false];
    }

    public function testGetBulkFilename(): void
    {
        $this->assertSame('deliveries.pdf', $this->builder->getBulkFilename());
    }
}
