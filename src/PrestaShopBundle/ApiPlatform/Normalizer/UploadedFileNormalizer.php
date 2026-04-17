<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Normalizer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * This normalizer disables the normalization process of File fields in the ApiPlatform resources
 * as recommended by ApiPlatform https://api-platform.com/docs/core/file-upload/.
 *
 * However, it does normalize and returns the content as an array so that we can use the miscellaneous
 * fields in our command mapping.
 */
class UploadedFileNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        return $data;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null)
    {
        return $data instanceof File;
    }

    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        if (!($object instanceof File)) {
            throw new InvalidArgumentException('Expected object to be a ' . File::class);
        }

        return [
            'type' => $object->getType(),
            'path' => $object->getPath(),
            'pathName' => $object->getPathname(),
            'realPath' => $object->getRealPath(),
            'mimeType' => $object->getMimeType(),
            'size' => $object->getSize(),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return $data instanceof File;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            File::class => true,
        ];
    }
}
