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
use PrestaShopBundle\Entity\Repository\CspLogRepository;

/**
 * The single collect/clear integration point, against a mocked repository: what gets normalized and
 * written, what untrusted browser input is dropped silently, and the per-shop row cap.
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
                self::SHOP_ID,
                'script-src',                 // directive lowercased
                'https://cdn.example.com',    // source reduced to origin
                'https://shop.example.com/page'
            )
            ->willReturn(true);
        $repository->method('countByShop')->willReturn(0);
        $repository->expects($this->never())->method('deleteLeastReportedByShop');

        $this->recorder($repository)->record(
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
        $repository->method('countByShop')->willReturn(0);
        $repository->expects($this->once())
            ->method('upsert')
            ->willReturnCallback(function (int $shopId, string $directive, string $source, ?string $documentUri) use (&$captured): bool {
                $captured = $documentUri;

                return true;
            });

        $this->recorder($repository)->record(self::SHOP_ID, 'script-src', 'https://cdn.example.com/app.js', $longUri);

        $this->assertNotNull($captured);
        $this->assertSame(2048, mb_strlen($captured), 'The document-uri is capped at 2048 chars');
    }

    public function testItStripsTheQueryStringAndFragmentFromTheDocumentUri(): void
    {
        $captured = null;

        $repository = $this->repository();
        $repository->method('countByShop')->willReturn(0);
        $repository->expects($this->once())
            ->method('upsert')
            ->willReturnCallback(function (int $shopId, string $directive, string $source, ?string $documentUri) use (&$captured): bool {
                $captured = $documentUri;

                return true;
            });

        // A documentURL carrying a reset token and an email must be reduced to scheme+host+path so it
        // never lands in the merchant-visible, exportable log.
        $this->recorder($repository)->record(
            self::SHOP_ID,
            'script-src',
            'https://cdn.example.com/app.js',
            'https://shop.example.com/password-reset?token=secret123&email=a@b.com#step2'
        );

        $this->assertSame('https://shop.example.com/password-reset', $captured);
    }

    public function testItDropsAnUnknownDirectiveWithoutWriting(): void
    {
        $repository = $this->repository();
        $repository->expects($this->never())->method('upsert');
        $repository->expects($this->never())->method('countByShop');

        $this->recorder($repository)->record(self::SHOP_ID, 'bogus-directive', 'https://cdn.example.com/app.js', null);
    }

    public function testItDropsAJunkSourceWithoutWriting(): void
    {
        $repository = $this->repository();
        $repository->expects($this->never())->method('upsert');

        $this->recorder($repository)->record(self::SHOP_ID, 'script-src', 'chrome-extension://abcdef/inject.js', null);
    }

    public function testItPrunesTheOverflowWhenTheShopIsOverTheRowCap(): void
    {
        $repository = $this->repository();
        $repository->method('upsert')->willReturn(true);
        $repository->method('countByShop')->willReturn(CspViolationRecorder::DEFAULT_ROW_CAP + 5);
        $repository->expects($this->once())
            ->method('deleteLeastReportedByShop')
            ->with(self::SHOP_ID, 5);

        $this->recorder($repository)->record(self::SHOP_ID, 'script-src', 'https://cdn.example.com/app.js', null);
    }

    public function testItDoesNotPruneWhenTheShopIsAtOrUnderTheRowCap(): void
    {
        $repository = $this->repository();
        $repository->method('upsert')->willReturn(true);
        $repository->method('countByShop')->willReturn(CspViolationRecorder::DEFAULT_ROW_CAP);
        $repository->expects($this->never())->method('deleteLeastReportedByShop');

        $this->recorder($repository)->record(self::SHOP_ID, 'script-src', 'https://cdn.example.com/app.js', null);
    }

    public function testItSkipsTheRowCapCheckWhenTheReportOnlyBumpsAnExistingRow(): void
    {
        $repository = $this->repository();
        $repository->method('upsert')->willReturn(false);
        $repository->expects($this->never())->method('countByShop');
        $repository->expects($this->never())->method('deleteLeastReportedByShop');

        $this->recorder($repository)->record(self::SHOP_ID, 'script-src', 'https://cdn.example.com/app.js', null);
    }

    public function testABatchCallerCanDeferTheRowCapAndRunItOnceForTheRequest(): void
    {
        $repository = $this->repository();
        $repository->method('upsert')->willReturn(true);
        // With $enforceCap = false the per-report path never touches the cap...
        $repository->expects($this->never())->method('countByShop');
        $repository->expects($this->never())->method('deleteLeastReportedByShop');

        $recorder = $this->recorder($repository);
        $recorder->record(self::SHOP_ID, 'script-src', 'https://a.example.com/x.js', null, false);
        $recorder->record(self::SHOP_ID, 'script-src', 'https://b.example.com/x.js', null, false);
    }

    public function testEnforceRowCapCanBeCalledOnceForAWholeBatch(): void
    {
        $repository = $this->repository();
        $repository->method('countByShop')->willReturn(CspViolationRecorder::DEFAULT_ROW_CAP + 3);
        // ...and the collector enforces it exactly once for the request.
        $repository->expects($this->once())->method('deleteLeastReportedByShop')->with(self::SHOP_ID, 3);

        $this->recorder($repository)->enforceRowCap(self::SHOP_ID);
    }

    public function testClearDelegatesToDeleteByShop(): void
    {
        $repository = $this->repository();
        $repository->expects($this->once())->method('deleteByShop')->with(self::SHOP_ID);

        $this->recorder($repository)->clear(self::SHOP_ID);
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
