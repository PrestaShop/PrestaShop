<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\CspViolationRecorder;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShopBundle\Entity\Repository\CspLogRepository;

/**
 * The single collect/clear integration point, against a mocked repository: what gets normalized and
 * written, what untrusted browser input is dropped silently, and the per-source page limit + per-shop
 * row cap (new sources refused once full, known sources keep counting, extra pages folded).
 */
class CspViolationRecorderTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testItUpsertsNormalizedValuesForAValidReport(): void
    {
        $repository = $this->repository();
        $repository->expects($this->once())
            ->method('upsert')
            ->with(
                CspContext::FRONT,
                self::SHOP_ID,
                'script-src',                 // directive lowercased
                'https://cdn.example.com',    // source reduced to origin
                'https://shop.example.com/page',
                null,                         // no sample/source-file/line for a host violation
                null,
                null
            )
            ->willReturn(true);

        $this->recorder($repository)->record(
            CspContext::FRONT,
            self::SHOP_ID,
            'SCRIPT-SRC',
            'https://cdn.example.com/app.js?v=1',
            'https://shop.example.com/page'
        );
    }

    public function testItTruncatesAnOverlongDocumentUri(): void
    {
        $longUri = 'https://shop.example.com/' . str_repeat('a', 3000);
        $captured = null;

        $repository = $this->repository();
        $repository->expects($this->once())
            ->method('upsert')
            ->willReturnCallback(function (CspContext $context, int $shopId, string $directive, string $source, ?string $documentUri) use (&$captured): bool {
                $captured = $documentUri;

                return true;
            });

        $this->recorder($repository)->record(CspContext::FRONT, self::SHOP_ID, 'script-src', 'https://cdn.example.com/app.js', $longUri);

        $this->assertNotNull($captured);
        $this->assertSame(255, mb_strlen($captured), 'The document-uri is capped at 255 chars (the stored column width)');
    }

    public function testItStripsTheQueryStringAndFragmentFromTheDocumentUri(): void
    {
        $captured = null;

        $repository = $this->repository();
        $repository->expects($this->once())
            ->method('upsert')
            ->willReturnCallback(function (CspContext $context, int $shopId, string $directive, string $source, ?string $documentUri) use (&$captured): bool {
                $captured = $documentUri;

                return true;
            });

        // A documentURL carrying a reset token and an email must be reduced to scheme+host+path so it
        // never lands in the merchant-visible, exportable log.
        $this->recorder($repository)->record(
            CspContext::FRONT,
            self::SHOP_ID,
            'script-src',
            'https://cdn.example.com/app.js',
            'https://shop.example.com/password-reset?token=secret123&email=a@b.com#step2'
        );

        $this->assertSame('https://shop.example.com/password-reset', $captured);
    }

    public function testItCapturesTheSampleSourceFileAndLineNumber(): void
    {
        $captured = [];

        $repository = $this->repository();
        $repository->expects($this->once())
            ->method('upsert')
            ->willReturnCallback(function (CspContext $context, int $shopId, string $directive, string $source, string $documentUri, ?string $sample, ?string $sourceFile, ?int $lineNumber) use (&$captured): bool {
                $captured = ['sample' => $sample, 'sourceFile' => $sourceFile, 'lineNumber' => $lineNumber];

                return true;
            });

        // An inline violation: the blocked source is the keyword, and the sample + source-file + line
        // tell the merchant which inline block fired. The sample is capped and the source file is stripped
        // of its query string, like the document URI.
        $this->recorder($repository)->record(
            CspContext::FRONT,
            self::SHOP_ID,
            'script-src',
            'inline',
            'https://shop.example.com/page',
            str_repeat('a', 300),
            'https://shop.example.com/page?token=secret#x',
            1481
        );

        $this->assertSame(64, mb_strlen((string) $captured['sample']), 'The sample is capped at 64 chars');
        $this->assertSame('https://shop.example.com/page', $captured['sourceFile'], 'The source file is query/fragment-stripped');
        $this->assertSame(1481, $captured['lineNumber']);
    }

    public function testItDropsANonPositiveLineNumber(): void
    {
        $captured = ['set' => false];

        $repository = $this->repository();
        $repository->expects($this->once())
            ->method('upsert')
            ->willReturnCallback(function (CspContext $context, int $shopId, string $directive, string $source, string $documentUri, ?string $sample, ?string $sourceFile, ?int $lineNumber) use (&$captured): bool {
                $captured = ['set' => true, 'lineNumber' => $lineNumber];

                return true;
            });

        $this->recorder($repository)->record(CspContext::FRONT, self::SHOP_ID, 'script-src', 'inline', null, null, null, 0);

        $this->assertTrue($captured['set']);
        $this->assertNull($captured['lineNumber'], 'A line number of 0 means "unknown" and is not stored');
    }

    public function testItDropsAnUnknownDirectiveWithoutWriting(): void
    {
        $repository = $this->repository();
        $repository->expects($this->never())->method('upsert');
        $repository->expects($this->never())->method('bumpIfExists');

        $this->recorder($repository)->record(CspContext::FRONT, self::SHOP_ID, 'bogus-directive', 'https://cdn.example.com/app.js', null);
    }

    public function testItDropsAJunkSourceWithoutWriting(): void
    {
        $repository = $this->repository();
        $repository->expects($this->never())->method('upsert');

        $this->recorder($repository)->record(CspContext::FRONT, self::SHOP_ID, 'script-src', 'chrome-extension://abcdef/inject.js', null);
    }

    public function testItBumpsAKnownSourceOnAKnownPageWithoutInserting(): void
    {
        $repository = $this->repository();
        // The exact (source, page) row already exists: bump it and record nothing new.
        $repository->method('bumpIfExists')->willReturn(true);
        $repository->expects($this->never())->method('sourceExists');
        $repository->expects($this->never())->method('upsert');

        $this->assertFalse($this->recorder($repository)->record(CspContext::FRONT, self::SHOP_ID, 'script-src', 'https://cdn.example.com/app.js', 'https://shop.example.com/a'));
    }

    public function testItRecordsANewPageAsAnExampleWhileUnderThePerSourceLimit(): void
    {
        $captured = null;
        $repository = $this->repository();
        $repository->method('bumpIfExists')->willReturn(false);
        $repository->method('sourceExists')->willReturn(true);
        $repository->method('hasAtLeastPages')->willReturn(false);
        $repository->expects($this->once())->method('upsert')->willReturnCallback(function (CspContext $c, int $s, string $d, string $src, string $documentUri) use (&$captured): bool {
            $captured = $documentUri;

            return true;
        });

        $this->recorder($repository)->record(CspContext::FRONT, self::SHOP_ID, 'script-src', 'https://cdn.example.com/app.js', 'https://shop.example.com/product-7');

        $this->assertSame('https://shop.example.com/product-7', $captured, 'A new page under the limit is kept as its own example row');
    }

    public function testItFoldsExtraPagesIntoTheOtherPagesRowOnceTheLimitIsReached(): void
    {
        $captured = 'unset';
        $repository = $this->repository();
        $repository->method('bumpIfExists')->willReturn(false);
        $repository->method('sourceExists')->willReturn(true);
        $repository->method('hasAtLeastPages')->willReturn(true);
        $repository->expects($this->once())->method('upsert')->willReturnCallback(function (CspContext $c, int $s, string $d, string $src, string $documentUri) use (&$captured): bool {
            $captured = $documentUri;

            return false;
        });

        $this->recorder($repository)->record(CspContext::FRONT, self::SHOP_ID, 'script-src', 'https://cdn.example.com/app.js', 'https://shop.example.com/product-999');

        $this->assertSame('', $captured, 'Once the per-source example limit is reached, further pages fold into the empty "other pages" row');
    }

    public function testItInsertsANewSourceBelowTheRowCap(): void
    {
        $repository = $this->repository();
        $repository->method('bumpIfExists')->willReturn(false);
        $repository->method('sourceExists')->willReturn(false);
        $repository->method('hasAtLeast')->with(CspContext::FRONT, self::SHOP_ID, CspViolationRecorder::DEFAULT_ROW_CAP)->willReturn(false);
        $repository->expects($this->once())->method('upsert')->willReturn(true);

        $this->assertTrue($this->recorder($repository)->record(CspContext::FRONT, self::SHOP_ID, 'script-src', 'https://cdn.example.com/app.js', null));
    }

    public function testItRefusesABrandNewSourceOnceTheShopIsAtTheRowCap(): void
    {
        $repository = $this->repository();
        $repository->method('bumpIfExists')->willReturn(false);
        $repository->method('sourceExists')->willReturn(false);
        $repository->method('hasAtLeast')->with(CspContext::FRONT, self::SHOP_ID, CspViolationRecorder::DEFAULT_ROW_CAP)->willReturn(true);
        $repository->expects($this->never())->method('upsert');

        $this->assertFalse($this->recorder($repository)->record(CspContext::FRONT, self::SHOP_ID, 'script-src', 'https://new.example.com/app.js', null));
    }

    public function testANonPositiveCapMeansNoCapForNewSources(): void
    {
        $repository = $this->repository();
        $repository->method('bumpIfExists')->willReturn(false);
        $repository->method('sourceExists')->willReturn(false);
        $repository->expects($this->never())->method('hasAtLeast');
        $repository->expects($this->once())->method('upsert')->willReturn(true);

        (new CspViolationRecorder($repository, 0))->record(CspContext::FRONT, self::SHOP_ID, 'script-src', 'https://cdn.example.com/app.js', null);
    }

    public function testEnforceRowCapTrimsTheOverflowForThePruneCommand(): void
    {
        $repository = $this->repository();
        $repository->method('countByShop')->willReturn(CspViolationRecorder::DEFAULT_ROW_CAP + 3);
        $repository->expects($this->once())->method('deleteLeastReportedByShop')->with(CspContext::FRONT, self::SHOP_ID, 3);

        $this->recorder($repository)->enforceRowCap(CspContext::FRONT, self::SHOP_ID);
    }

    public function testClearDelegatesToDeleteByShop(): void
    {
        $repository = $this->repository();
        $repository->expects($this->once())->method('deleteByShop')->with(CspContext::FRONT, self::SHOP_ID);

        $this->recorder($repository)->clear(CspContext::FRONT, self::SHOP_ID);
    }

    private function recorder(CspLogRepository $repository): CspViolationRecorder
    {
        return new CspViolationRecorder($repository);
    }

    private function repository(): CspLogRepository&MockObject
    {
        return $this->createMock(CspLogRepository::class);
    }
}
