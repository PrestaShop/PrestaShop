<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Normalizer;

use PrestaShop\PrestaShop\Core\Util\DateTime\DateImmutable;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes DateImmutable properties with Y-m-d format for API Platform.
 * Ensures date-only values are serialized without time component.
 */
#[AutoconfigureTag('prestashop.api.normalizers')]
class DateImmutableNormalizer implements DenormalizerInterface, NormalizerInterface
{
    /**
     * @param mixed $data Date string in Y-m-d format
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        return new DateImmutable($data);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return DateImmutable::class === $type;
    }

    /**
     * @param mixed $object Must be a DateImmutable instance
     *
     * @return string Date string in Y-m-d format
     */
    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        if (!($object instanceof DateImmutable)) {
            throw new InvalidArgumentException('Expected object to be a ' . DateImmutable::class);
        }

        return $object->format(DateTimeUtil::DEFAULT_DATE_FORMAT);
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return $data instanceof DateImmutable;
    }

    /**
     * @return array<string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            DateImmutable::class => true,
        ];
    }
}
