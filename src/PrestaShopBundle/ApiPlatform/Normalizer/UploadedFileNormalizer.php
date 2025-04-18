<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
