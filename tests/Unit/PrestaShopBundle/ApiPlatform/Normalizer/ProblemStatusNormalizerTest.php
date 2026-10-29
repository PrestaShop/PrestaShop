<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Normalizer;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShopBundle\ApiPlatform\Normalizer\ProblemStatusNormalizer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ProblemNormalizer;

class ProblemStatusNormalizerTest extends TestCase
{
    public function testMappedStatusAndDescription(): void
    {
        $normalizer = new ProblemStatusNormalizer(new ProblemNormalizer(false));
        $exception = FlattenException::createFromThrowable(new ZoneNotFoundException('Zone #9999 not found'));

        $payload = $normalizer->normalize($exception, 'json', ['statusCode' => Response::HTTP_NOT_FOUND]);

        $this->assertSame(Response::HTTP_NOT_FOUND, $payload['status']);
        $this->assertSame('Zone not found', $payload['detail']);
    }

    public function testUnmappedExceptionIsUnchanged(): void
    {
        $normalizer = new ProblemStatusNormalizer(new ProblemNormalizer(false));
        $exception = FlattenException::createFromThrowable(new ZoneNotFoundException());

        $payload = $normalizer->normalize($exception, 'json', ['statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR]);

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $payload['status']);
        $this->assertSame('Internal Server Error', $payload['detail']);
    }
}
