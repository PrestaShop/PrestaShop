<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Normalizer;

use ApiPlatform\Metadata\Get;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\BulkFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShopBundle\ApiPlatform\Normalizer\BulkCommandExceptionNormalizer;
use RuntimeException;
use stdClass;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class BulkCommandExceptionNormalizerTest extends TestCase
{
    public function testSupportsNormalizationWithDirectBulkException(): void
    {
        $bulkException = new BulkFeatureException(
            [new FeatureException('error')],
            'Bulk error'
        );
        $normalizer = $this->createNormalizerWithException($bulkException);
        $flattenException = FlattenException::createFromThrowable($bulkException);

        $this->assertTrue($normalizer->supportsNormalization($flattenException));
    }

    public function testSupportsNormalizationWithWrappedBulkException(): void
    {
        $bulkException = new BulkFeatureException(
            [new FeatureException('error')],
            'Bulk error'
        );
        $httpException = new HttpException(Response::HTTP_MULTI_STATUS, 'Bulk error', $bulkException);
        $normalizer = $this->createNormalizerWithException($httpException);
        $flattenException = FlattenException::createFromThrowable($httpException);

        $this->assertTrue($normalizer->supportsNormalization($flattenException));
    }

    public function testSupportsNormalizationReturnsFalseForNonBulkException(): void
    {
        $runtimeException = new RuntimeException('error');
        $normalizer = $this->createNormalizerWithException($runtimeException);
        $flattenException = FlattenException::createFromThrowable($runtimeException);

        $this->assertFalse($normalizer->supportsNormalization($flattenException));
    }

    public function testSupportsNormalizationReturnsFalseForNonFlattenException(): void
    {
        $normalizer = $this->createNormalizerWithException(null);

        $this->assertFalse($normalizer->supportsNormalization(new stdClass()));
    }

    public function testSupportsNormalizationReturnsFalseWithNoRequest(): void
    {
        $requestStack = new RequestStack();
        $normalizer = new BulkCommandExceptionNormalizer($requestStack);
        $flattenException = FlattenException::createFromThrowable(new RuntimeException('error'));

        $this->assertFalse($normalizer->supportsNormalization($flattenException));
    }

    public function testNormalizeUsesExceptionToStatusFromOperation(): void
    {
        $innerExceptions = [
            new FeatureNotFoundException('Feature #1 not found'),
            new FeatureException('Feature #2 cannot be deleted'),
        ];
        $bulkException = new BulkFeatureException(
            $innerExceptions,
            'Errors occurred during Feature bulk delete action',
            BulkFeatureException::FAILED_BULK_DELETE
        );

        $exceptionToStatus = [
            FeatureNotFoundException::class => Response::HTTP_NOT_FOUND,
        ];

        $normalizer = $this->createNormalizerWithException($bulkException, $exceptionToStatus);
        $flattenException = FlattenException::createFromThrowable($bulkException);

        $result = $normalizer->normalize($flattenException);

        $this->assertSame(BulkFeatureException::class, $result['type']);
        $this->assertSame(Response::HTTP_MULTI_STATUS, $result['status']);

        $this->assertCount(2, $result['errors']);

        $this->assertSame(FeatureNotFoundException::class, $result['errors'][0]['type']);
        $this->assertSame(Response::HTTP_NOT_FOUND, $result['errors'][0]['status']);
        $this->assertSame('Feature #1 not found', $result['errors'][0]['message']);

        $this->assertSame(FeatureException::class, $result['errors'][1]['type']);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $result['errors'][1]['status']);
        $this->assertSame('Feature #2 cannot be deleted', $result['errors'][1]['message']);
    }

    public function testNormalizeDefaultsTo500WhenNoExceptionToStatus(): void
    {
        $bulkException = new BulkFeatureException(
            [new FeatureException('error')],
            'Bulk error'
        );

        $normalizer = $this->createNormalizerWithException($bulkException);
        $flattenException = FlattenException::createFromThrowable($bulkException);

        $result = $normalizer->normalize($flattenException);

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $result['errors'][0]['status']);
    }

    public function testNormalizeWithWrappedBulkException(): void
    {
        $bulkException = new BulkFeatureException(
            [new FeatureException('Only one failed')],
            'Bulk error'
        );
        $httpException = new HttpException(Response::HTTP_MULTI_STATUS, 'Bulk error', $bulkException);

        $normalizer = $this->createNormalizerWithException($httpException);
        $flattenException = FlattenException::createFromThrowable($httpException);

        $result = $normalizer->normalize($flattenException);

        $this->assertSame(BulkFeatureException::class, $result['type']);
        $this->assertSame(Response::HTTP_MULTI_STATUS, $result['status']);
        $this->assertCount(1, $result['errors']);
        $this->assertSame('Only one failed', $result['errors'][0]['message']);
    }

    public function testGetSupportedTypes(): void
    {
        $normalizer = $this->createNormalizerWithException(null);

        $supportedTypes = $normalizer->getSupportedTypes(null);

        $this->assertArrayHasKey(FlattenException::class, $supportedTypes);
        $this->assertFalse($supportedTypes[FlattenException::class]);
    }

    private function createNormalizerWithException(?Throwable $exception, array $exceptionToStatus = []): BulkCommandExceptionNormalizer
    {
        $requestStack = new RequestStack();

        $request = new Request();
        if ($exception !== null) {
            $request->attributes->set('exception', $exception);
        }
        if (!empty($exceptionToStatus)) {
            $request->attributes->set('_api_previous_operation', new Get(exceptionToStatus: $exceptionToStatus));
        }
        $requestStack->push($request);

        return new BulkCommandExceptionNormalizer($requestStack);
    }
}
