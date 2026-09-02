<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Normalizer;

use ApiPlatform\Metadata\HttpOperation;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Throwable;

/**
 * Normalizes BulkCommandExceptionInterface errors into a multi-status response
 * containing the list of individual errors that occurred during the bulk operation.
 *
 * In the API Platform error flow, the serializer receives a FlattenException which loses
 * the original exception's getExceptions() data. The original BulkCommandExceptionInterface
 * is retrieved from the request attributes where Symfony's ErrorListener stores it.
 * It may be stored directly or as the previous exception of an HttpException wrapper
 * (set by BulkCommandExceptionListener).
 */
class BulkCommandExceptionNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $bulkException = $this->getBulkException();
        $exceptionToStatus = $this->getExceptionToStatus();

        return [
            'type' => get_class($bulkException),
            'status' => Response::HTTP_MULTI_STATUS,
            'message' => $bulkException->getMessage(),
            'errors' => array_map(
                fn (Throwable $e) => [
                    'type' => get_class($e),
                    'status' => $this->resolveExceptionStatus($e, $exceptionToStatus),
                    'message' => $e->getMessage(),
                ],
                $bulkException->getExceptions()
            ),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof FlattenException) {
            return false;
        }

        return $this->getBulkException() !== null;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            FlattenException::class => false,
        ];
    }

    /**
     * @param array<class-string, int> $exceptionToStatus
     */
    private function resolveExceptionStatus(Throwable $exception, array $exceptionToStatus): int
    {
        foreach ($exceptionToStatus as $class => $status) {
            if (is_a($exception, $class)) {
                return $status;
            }
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    private function getExceptionToStatus(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $operation = $request?->attributes->get('_api_previous_operation')
            ?? $request?->attributes->get('_api_operation');

        if ($operation instanceof HttpOperation) {
            return $operation->getExceptionToStatus() ?? [];
        }

        return [];
    }

    private function getBulkException(): ?BulkCommandExceptionInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        $exception = $request->attributes->get('exception');
        if ($exception instanceof BulkCommandExceptionInterface) {
            return $exception;
        }

        // When wrapped in HttpException by BulkCommandExceptionListener, the original
        // BulkCommandExceptionInterface is stored as the previous exception
        if ($exception instanceof Throwable && $exception->getPrevious() instanceof BulkCommandExceptionInterface) {
            return $exception->getPrevious();
        }

        return null;
    }
}
