<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Normalizer;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalize DecimalNumber values
 */
#[AutoconfigureTag('prestashop.api.normalizers')]
class ShopConstraintNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if (!empty($data['shopId'])) {
            return ShopConstraint::shop($data['shopId'], $data['isStrict'] ?? false);
        }
        if (!empty($data['shopGroupId'])) {
            return ShopConstraint::shopGroup($data['shopGroupId'], $data['isStrict'] ?? false);
        }
        if (!empty($data['shopIds'])) {
            return ShopCollection::shops($data['shopIds']);
        }

        return ShopConstraint::allShops($data['isStrict'] ?? false);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return ShopConstraint::class === $type || is_subclass_of($type, ShopConstraint::class);
    }

    public function normalize($object, ?string $format = null, array $context = [])
    {
        if (!($object instanceof ShopConstraint)) {
            throw new InvalidArgumentException('Expected object to be a ' . ShopConstraint::class);
        }

        return [
            'shopId' => $object->getShopId()?->getValue(),
            'shopGroupId' => $object->getShopGroupId()?->getValue(),
            'shopIds' => $object instanceof ShopCollection ? array_map(fn (ShopId $shopId) => $shopId->getValue(), $object->getShopIds()) : null,
            'isStrict' => $object->isStrict(),
        ];
    }

    public function supportsNormalization($data, ?string $format = null)
    {
        return $data instanceof ShopConstraint;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            ShopConstraint::class => true,
            ShopCollection::class => true,
        ];
    }
}
