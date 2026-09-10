<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Normalizer;

use PrestaShop\Decimal\DecimalNumber;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalize DecimalNumber values
 */
#[AutoconfigureTag('prestashop.api.normalizers')]
class DecimalNumberNormalizer implements DenormalizerInterface, NormalizerInterface
{
    /**
     * A value that is not a number raises the serializer's own exception (a 400 with a message
     * on the API, and the next member of a union type gets its chance) instead of the decimal
     * library's InvalidArgumentException, which the serializer would not recognize (500).
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof DecimalNumber) {
            return $data;
        }

        if (is_scalar($data) && !is_bool($data)) {
            try {
                return new DecimalNumber((string) $data);
            } catch (\InvalidArgumentException $e) {
                // Falls through to the exception below.
            }
        }

        throw NotNormalizableValueException::createForUnexpectedDataType(
            sprintf('The value %s cannot be interpreted as a number.', is_scalar($data) ? var_export($data, true) : get_debug_type($data)),
            $data,
            ['number'],
            $context['deserialization_path'] ?? null,
            true
        );
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return DecimalNumber::class === $type;
    }

    public function normalize($object, ?string $format = null, array $context = [])
    {
        if (!($object instanceof DecimalNumber)) {
            throw new InvalidArgumentException('Expected object to be a ' . DecimalNumber::class);
        }

        return (float) (string) $object;
    }

    public function supportsNormalization($data, ?string $format = null)
    {
        return $data instanceof DecimalNumber;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            DecimalNumber::class => true,
        ];
    }
}
