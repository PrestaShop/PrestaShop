<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Component;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\PDF\GeneratedPdf;
use PrestaShopBundle\Component\PdfResponse;

class PdfResponseTest extends TestCase
{
    #[DataProvider('getFileNames')]
    public function testItSendsThePdfAsAttachment(string $fileName, string $expectedDisposition): void
    {
        $response = new PdfResponse(new GeneratedPdf('%PDF-1.7', $fileName));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('%PDF-1.7', $response->getContent());
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertSame($expectedDisposition, $response->headers->get('Content-Disposition'));
    }

    public static function getFileNames(): iterable
    {
        yield 'ascii' => ['IN000001.pdf', 'attachment; filename=IN000001.pdf'];
        yield 'non ascii prefix' => ['FA€é000001.pdf', 'attachment; filename=FAEURe000001.pdf; filename*=utf-8\'\'FA%E2%82%AC%C3%A9000001.pdf'];
        yield 'slash in prefix' => ['IN/2026/000001.pdf', 'attachment; filename=IN-2026-000001.pdf'];
        yield 'transliterated to a slash' => ['FA½000001.pdf', 'attachment; filename="FA 1-2000001.pdf"; filename*=utf-8\'\'FA%C2%BD000001.pdf'];
        yield 'percent in prefix' => ['IN%000001.pdf', 'attachment; filename=IN000001.pdf; filename*=utf-8\'\'IN%25000001.pdf'];
    }
}
