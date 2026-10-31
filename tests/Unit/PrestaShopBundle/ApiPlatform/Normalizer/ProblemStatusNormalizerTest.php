<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Normalizer;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\ApiPlatform\Normalizer\ProblemStatusNormalizer;
use RuntimeException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProblemStatusNormalizerTest extends TestCase
{
    public function testTheMappedStatusIsHandedToTheDecoratedNormalizer(): void
    {
        $capturedContext = null;
        $normalizer = new ProblemStatusNormalizer($this->buildDecorated($capturedContext));

        // ApiPlatform's ExceptionAction resolves the domain exception to its mapped status and passes
        // it as statusCode, while the exception itself still reports 500.
        $normalizer->normalize(FlattenException::createFromThrowable(new RuntimeException('not found')), 'jsonproblem', ['statusCode' => 404]);

        $this->assertSame(404, $capturedContext['status']);
    }

    public function testAnExplicitStatusIsNotOverwritten(): void
    {
        $capturedContext = null;
        $normalizer = new ProblemStatusNormalizer($this->buildDecorated($capturedContext));

        $normalizer->normalize(FlattenException::createFromThrowable(new RuntimeException('nope')), 'jsonproblem', ['statusCode' => 404, 'status' => 410]);

        $this->assertSame(410, $capturedContext['status']);
    }

    public function testAContextWithoutAStatusCodeIsLeftAlone(): void
    {
        $capturedContext = null;
        $normalizer = new ProblemStatusNormalizer($this->buildDecorated($capturedContext));

        $normalizer->normalize(FlattenException::createFromThrowable(new RuntimeException('nope')), 'jsonproblem', []);

        $this->assertArrayNotHasKey('status', $capturedContext);
    }

    private function buildDecorated(&$capturedContext): NormalizerInterface
    {
        $decorated = $this->createMock(NormalizerInterface::class);
        $decorated->method('normalize')->willReturnCallback(
            function ($object, ?string $format = null, array $context = []) use (&$capturedContext): array {
                $capturedContext = $context;

                return [];
            }
        );

        return $decorated;
    }
}
