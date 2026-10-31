<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Normalizer;

use ArrayObject;
use PrestaShopBundle\ApiPlatform\ExceptionDescriptionTrait;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ProblemNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * API Platform answers the status of its exceptionToStatus map but passes it to the serializer as the "statusCode"
 * context, while Symfony's ProblemNormalizer reads the status of the FlattenException (500 for any exception that is
 * not an HTTP one): the status and the detail of the payload contradicted the response status. The detail (without debug
 * mode) describes the mapped exception, like the documented responses.
 */
class ProblemStatusNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use ExceptionDescriptionTrait;

    public function __construct(
        private readonly ProblemNormalizer $decorated,
    ) {
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|ArrayObject|null
    {
        $statusCode = $context['statusCode'] ?? null;
        if ($object instanceof FlattenException && is_int($statusCode) && $statusCode !== $object->getStatusCode()) {
            $object->setStatusCode($statusCode);
            $object->setStatusText($this->getExceptionDescription($object->getClass()));
        }

        return $this->decorated->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->decorated->getSupportedTypes($format);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->decorated->setSerializer($serializer);
    }
}
