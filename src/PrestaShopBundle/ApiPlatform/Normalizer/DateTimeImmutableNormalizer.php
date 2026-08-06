<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Normalizer;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalize DateTimeImmutable properties.
 */
#[AutoconfigureTag('prestashop.api.normalizers')]
class DateTimeImmutableNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if (DateTimeUtil::isNull($data)) {
            return null;
        }

        return new DateTimeImmutable($data);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return DateTimeImmutable::class === $type;
    }

    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        if (!($object instanceof DateTimeImmutable)) {
            throw new InvalidArgumentException('Expected object to be a ' . DateTimeImmutable::class);
        }

        return $object->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return $data instanceof DateTimeImmutable;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            DateTimeImmutable::class => true,
        ];
    }
}
