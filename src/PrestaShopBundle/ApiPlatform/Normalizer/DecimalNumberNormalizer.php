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
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalize DecimalNumber values
 */
#[AutoconfigureTag('prestashop.api.normalizers')]
class DecimalNumberNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        return new DecimalNumber((string) $data);
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
