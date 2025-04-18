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

namespace PrestaShopBundle\ApiPlatform\Serializer;

use PrestaShopBundle\ApiPlatform\ContextParametersProvider;
use PrestaShopBundle\ApiPlatform\LocalizedValueUpdater;
use PrestaShopBundle\ApiPlatform\Metadata\LocalizedValue;
use PrestaShopBundle\ApiPlatform\NormalizationMapper;
use ReflectionNamedType;
use Symfony\Component\Serializer\Encoder\ContextAwareDecoderInterface;
use Symfony\Component\Serializer\Encoder\ContextAwareEncoderInterface;
use Symfony\Component\Serializer\Exception\UnsupportedFormatException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * This serializer decorates the API Platform one, it handles PrestaShop custom modifications like updating the localized values indexes,
 * or apply the mapping between CQRS object and API resources.
 */
class CQRSApiSerializer implements SerializerInterface, ContextAwareNormalizerInterface, ContextAwareDenormalizerInterface, ContextAwareEncoderInterface, ContextAwareDecoderInterface
{
    public const CAST_BOOL = 'cast_bool';

    public function __construct(
        protected readonly Serializer $decorated,
        protected readonly ContextParametersProvider $contextParametersProvider,
        protected readonly ClassMetadataFactoryInterface $classMetadataFactory,
        protected readonly LocalizedValueUpdater $localizedValueUpdater,
        protected readonly NormalizationMapper $normalizationMapper,
    ) {
    }

    public function supportsDecoding(string $format, array $context = []): bool
    {
        return $this->decorated->supportsDecoding($format, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $this->decorated->supportsDenormalization($data, $type, $format, $context);
    }

    public function supportsEncoding(string $format, array $context = []): bool
    {
        return $this->decorated->supportsEncoding($format, $context);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    public function decode(string $data, string $format, array $context = [])
    {
        return $this->decorated->decode($data, $format, $context);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        // Add context parameters and URI variables into the data
        $data = array_merge($data, $this->contextParametersProvider->getContextParameters());
        if (!empty($context['uri_variables'])) {
            $data = array_merge($data, $context['uri_variables']);
        }

        // Before anything perform the mapping if specified
        $this->normalizationMapper->mapNormalizedData($data, $context);

        // Boolean casting is only implemented in Serializer 7.1, so for now we handle it manually
        // This is important for list results mainly where boolean are returned as tiny int (0, 1)
        $this->addBooleanCastCallbacks($type, $context);

        // Update localized value to be adapted for denormalization
        if (is_array($data)) {
            $data = $this->updateLocalizedValues($data, $type, true, $context);
        }

        return $this->decorated->denormalize($data, $type, $format, $context);
    }

    public function encode(mixed $data, string $format, array $context = []): string
    {
        return $this->decorated->encode($data, $format, $context);
    }

    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        // First let the usual serializer and normalizers do their job
        $normalizedData = $this->decorated->normalize($object, $format, $context);

        // Then update the localized values to use the appropriate indexes
        if (is_object($object) && class_exists(get_class($object))) {
            $normalizedData = $this->updateLocalizedValues($normalizedData, get_class($object), false, $context);
        }

        // Finally perform normalization mapping
        $this->normalizationMapper->mapNormalizedData($normalizedData, $context);

        return $normalizedData;
    }

    public function serialize(mixed $data, string $format, array $context = []): string
    {
        return $this->decorated->serialize($data, $format, $context);
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        if (!$this->supportsDecoding($format, $context)) {
            throw new UnsupportedFormatException(sprintf('Deserialization for the format "%s" is not supported.', $format));
        }

        $data = $this->decode($data, $format, $context);

        return $this->denormalize($data, $type, $format, $context);
    }

    /**
     * Adapt data for localized values so that the indexes match the expected value (ID or locale)
     */
    protected function updateLocalizedValues(array $data, string $type, bool $denormalize, array $context = []): array
    {
        $localizedAttributesContext = $this->localizedValueUpdater->getLocalizedAttributesContext($type);
        if (!empty($localizedAttributesContext)) {
            foreach ($localizedAttributesContext as $parameterName => $attributeContext) {
                if (!empty($data[$parameterName])) {
                    if ($denormalize) {
                        $data[$parameterName] = $this->localizedValueUpdater->denormalizeLocalizedValue(
                            $data[$parameterName],
                            $parameterName,
                            $context + [LocalizedValue::IS_LOCALIZED_VALUE => true] + $attributeContext
                        );
                    } else {
                        $data[$parameterName] = $this->localizedValueUpdater->normalizeLocalizedValue(
                            $data[$parameterName],
                            $parameterName,
                            $context + [LocalizedValue::IS_LOCALIZED_VALUE => true] + $attributeContext
                        );
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Force casting boolean properties so that values like (1, 0, true, on, false, ...) are valid, this is useful for
     * data coming from DB where boolean are returned as tiny integers. To enable this casting the CQRSApiSerializer::CAST_BOOL
     * context option must be true.
     *
     * Note: in Symfony 7.1 a new option AbstractNormalizer::FILTER_BOOL has been introduced, when we upgrade our
     * Symfony dependencies our custom casting (inspired by the Symfony one) can be removed.
     *
     * https://symfony.com/doc/7.1/serializer.html#handling-boolean-values
     */
    protected function addBooleanCastCallbacks(string $type, array &$context): void
    {
        if (empty($context[self::CAST_BOOL])) {
            return;
        }

        if (!$this->classMetadataFactory->hasMetadataFor($type)) {
            return;
        }

        $metadata = $this->classMetadataFactory->getMetadataFor($type);
        foreach ($metadata->getAttributesMetadata() as $attributeMetadata) {
            if (!$metadata->getReflectionClass()->hasProperty($attributeMetadata->getName())) {
                continue;
            }

            $reflectionProperty = $metadata->getReflectionClass()->getProperty($attributeMetadata->getName());
            if ($reflectionProperty->getType() instanceof ReflectionNamedType && $reflectionProperty->getType()->getName() === 'bool') {
                $context[AbstractNormalizer::CALLBACKS][$attributeMetadata->getName()] = fn (mixed $value): bool => filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
        }
    }
}
