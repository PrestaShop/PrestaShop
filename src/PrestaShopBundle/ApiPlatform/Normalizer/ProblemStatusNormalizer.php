<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Reports the status the response actually carries in the error body.
 *
 * WHY: API Platform maps a domain exception to its HTTP status and hands that status to the
 * serializer under the `statusCode` key (ExceptionAction), while Symfony's ProblemNormalizer reads
 * it from `status` and otherwise falls back to the status of the exception itself, which is 500 for
 * any domain exception. A mapped response therefore answers 404 while its body says 500.
 */
final class ProblemStatusNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly NormalizerInterface $decorated,
    ) {
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): mixed
    {
        if (isset($context['statusCode']) && !isset($context['status'])) {
            $context['status'] = $context['statusCode'];
        }

        return $this->decorated->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, ?string $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    /**
     * @return array<class-string|'*'|'object'|string, bool|null>
     */
    public function getSupportedTypes(?string $format): array
    {
        return $this->decorated->getSupportedTypes($format);
    }
}
