<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\ApiPlatform\Serializer;

use ApiPlatform\Metadata\HttpOperation;
use PrestaShopBundle\ApiPlatform\ContextParametersProvider;
use PrestaShopBundle\ApiPlatform\DefaultValuesTrait;
use PrestaShopBundle\ApiPlatform\LocalizedValueUpdater;
use PrestaShopBundle\ApiPlatform\Metadata\LocalizedValue;
use PrestaShopBundle\ApiPlatform\NormalizationMapper;
use PrestaShopBundle\ApiPlatform\Normalizer\CQRSApiNormalizer;
use PrestaShopBundle\ApiPlatform\PositionCollectionUpdater;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Encoder\ContextAwareDecoderInterface;
use Symfony\Component\Serializer\Encoder\ContextAwareEncoderInterface;
use Symfony\Component\Serializer\Exception\UnsupportedFormatException;
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
    use DefaultValuesTrait;

    public const CAST_BOOL = 'cast_bool';

    public function __construct(
        protected readonly Serializer $decorated,
        protected readonly ContextParametersProvider $contextParametersProvider,
        protected readonly LocalizedValueUpdater $localizedValueUpdater,
        protected readonly NormalizationMapper $normalizationMapper,
        protected readonly PositionCollectionUpdater $positionCollectionUpdater,
        protected readonly PropertyTypeExtractorInterface $propertyTypeExtractor,
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
        // Usually empty body would trigger an exception, unless we allowed it via the custom extra property
        if ($this->isEmptyBodyAllowed($data, $context)) {
            return [];
        }

        return $this->decorated->decode($data, $format, $context);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        // Add context parameters and URI variables into the data
        $data = array_merge($data, $this->contextParametersProvider->getContextParameters());
        if (!empty($context['uri_variables'])) {
            $data = array_merge($data, $context['uri_variables']);
        }

        // The default values of the operation are injected first, so they are mapped and validated exactly like the
        // values that were part of the request. Only a request body is concerned here, which is why an input class is
        // expected in the context: the queries are built by the providers, and the returned resources must not be
        // filled with the default values of an input.
        if (!empty($context['input']['class'])) {
            $data = $this->applyDefaultValues($data, $context['operation'] ?? null);
        }

        // A request body is converted into a CQRS command based on language IDs: the localized values of its sub objects
        // are converted before the mapping, so that the mapped copies are converted too (the root ones are handled by the
        // CQRSApiNormalizer). The resource is validated on the request values, so the data before the conversion is kept.
        $isCommandInput = is_array($data) && !empty($context['input']['class']) && $context['input']['class'] !== $type;
        if ($isCommandInput) {
            $resourceData = $data;
            $resourceContext = $context;
            $this->normalizationMapper->mapNormalizedData($resourceData, $resourceContext);
            $context[CQRSApiNormalizer::RESOURCE_DATA] = $this->denormalizeLocalizedValues($resourceData, $type, $resourceContext);
            $data = $this->updateSubObjectsLocalizedValues($data, $type, [LocalizedValue::DENORMALIZED_KEY => LocalizedValue::ID_KEY], true);
        }

        // Before anything perform the mapping if specified
        $this->normalizationMapper->mapNormalizedData($data, $context);

        // Boolean casting is only implemented in Serializer 7.1, so for now we handle it manually
        // This is important for query results where boolean are returned as tiny int (0, 1)
        if (!empty($context[self::CAST_BOOL]) && is_array($data)) {
            $data = $this->castBooleans($data, $type);
        }

        // Update localized value to be adapted for denormalization
        if (is_array($data)) {
            $data = $this->denormalizeLocalizedValues($data, $type, $context);
            if (!$isCommandInput) {
                $data = $this->updateSubObjectsLocalizedValues($data, $type, $context, true);
            }
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
            $normalizedData = $this->normalizeLocalizedValues($normalizedData, get_class($object), $context);
            if (is_array($normalizedData)) {
                $normalizedData = $this->updateSubObjectsLocalizedValues($normalizedData, get_class($object), $context, false);
            }
            $normalizedData = $this->positionCollectionUpdater->normalizePositionCollection($normalizedData, get_class($object));
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
     * Denormalize data for localized values so that the indexes match the expected value (ID or locale)
     */
    protected function denormalizeLocalizedValues(array $data, string $type, array $context = []): array
    {
        $localizedAttributesContext = $this->localizedValueUpdater->getLocalizedAttributesContext($type);
        if (!empty($localizedAttributesContext)) {
            foreach ($localizedAttributesContext as $parameterName => $attributeContext) {
                if (!empty($data[$parameterName])) {
                    $data[$parameterName] = $this->localizedValueUpdater->denormalizeLocalizedValue(
                        $data[$parameterName],
                        $parameterName,
                        $context + [LocalizedValue::IS_LOCALIZED_VALUE => true] + $attributeContext
                    );
                }
            }
        }

        return $data;
    }

    /**
     * Normalize data for localized values so that the indexes match the expected value (ID or locale)
     */
    protected function normalizeLocalizedValues(array $data, string $type, array $context = []): array
    {
        $localizedAttributesContext = $this->localizedValueUpdater->getLocalizedAttributesContext($type);
        if (!empty($localizedAttributesContext)) {
            foreach ($localizedAttributesContext as $parameterName => $attributeContext) {
                if (!empty($data[$parameterName])) {
                    $data[$parameterName] = $this->localizedValueUpdater->normalizeLocalizedValue(
                        $data[$parameterName],
                        $parameterName,
                        $context + [LocalizedValue::IS_LOCALIZED_VALUE => true] + $attributeContext
                    );
                }
            }
        }

        return $data;
    }

    /**
     * The localized values of the sub objects of the API resources (DTOs, and lists of DTOs typed with a PHPDoc like
     * Foo[]) are converted like the ones of the resource, at every level.
     */
    protected function updateSubObjectsLocalizedValues(array $data, string $type, array $context, bool $denormalize): array
    {
        if (!class_exists($type)) {
            return $data;
        }

        // The mapping and the localized parameters of the context are those of the root object
        $subObjectContext = array_diff_key($context, [
            NormalizationMapper::NORMALIZATION_MAPPING => true,
            NormalizationMapper::APPLIED_NORMALIZATION_MAPPING => true,
            LocalizedValue::LOCALIZED_VALUE_PARAMETERS => true,
            'operation' => true,
        ]);
        foreach ($data as $property => $value) {
            if (!is_string($property) || !is_array($value) || empty($value)) {
                continue;
            }

            foreach ($this->propertyTypeExtractor->getTypes($type, $property) ?? [] as $propertyType) {
                $subObjectClass = $propertyType->getClassName();
                if (null !== $subObjectClass && $this->isApiResourceSubObject($subObjectClass)) {
                    $data[$property] = $this->updateLocalizedValues($value, $subObjectClass, $subObjectContext, $denormalize);
                    break;
                }

                $itemClass = $propertyType->isCollection() ? ($propertyType->getCollectionValueTypes()[0] ?? null)?->getClassName() : null;
                if (null !== $itemClass && $this->isApiResourceSubObject($itemClass)) {
                    foreach ($value as $key => $item) {
                        if (is_array($item)) {
                            $data[$property][$key] = $this->updateLocalizedValues($item, $itemClass, $subObjectContext, $denormalize);
                        }
                    }
                    break;
                }
            }
        }

        return $data;
    }

    protected function updateLocalizedValues(array $data, string $type, array $context, bool $denormalize): array
    {
        $data = $denormalize ? $this->denormalizeLocalizedValues($data, $type, $context) : $this->normalizeLocalizedValues($data, $type, $context);

        return $this->updateSubObjectsLocalizedValues($data, $type, $context, $denormalize);
    }

    protected function isApiResourceSubObject(string $class): bool
    {
        return str_contains($class, '\\ApiPlatform\\Resources\\');
    }

    /**
     * Casts the values of the boolean properties (1, 0, "1", "true", "on", ...) when the CQRSApiSerializer::CAST_BOOL
     * context option is true, useful for query results where booleans are returned as tiny integers. The nested
     * objects and the lists of objects (typed with a PHPDoc like Foo[]) are cast too. A value that doesn't
     * represent a boolean is left untouched, so the denormalization still rejects it.
     *
     * Note: in Symfony 7.1 a new option AbstractNormalizer::FILTER_BOOL has been introduced, but it doesn't cast
     * integers.
     */
    protected function castBooleans(array $data, string $type): array
    {
        if (!class_exists($type)) {
            return $data;
        }

        foreach ($data as $property => $value) {
            if (!is_string($property) || null === $value) {
                continue;
            }

            $types = $this->propertyTypeExtractor->getTypes($type, $property);
            // Union types are ambiguous, the value is left as is
            if (null === $types || count($types) !== 1) {
                continue;
            }

            $propertyType = $types[0];
            if (Type::BUILTIN_TYPE_BOOL === $propertyType->getBuiltinType()) {
                $data[$property] = $this->castBoolean($value);
            } elseif (is_array($value) && null !== $propertyType->getClassName()) {
                $data[$property] = $this->castBooleans($value, $propertyType->getClassName());
            } elseif (is_array($value) && $propertyType->isCollection()) {
                $itemClass = ($propertyType->getCollectionValueTypes()[0] ?? null)?->getClassName();
                if (null !== $itemClass) {
                    foreach ($value as $key => $item) {
                        if (is_array($item)) {
                            $data[$property][$key] = $this->castBooleans($item, $itemClass);
                        }
                    }
                }
            }
        }

        return $data;
    }

    protected function castBoolean(mixed $value): mixed
    {
        if (!is_scalar($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    /**
     * Empty body is not allowed with JSON format as empty string is considered invalid JSON, but in some cases we
     * want to send an empty body (delete an operation, enable an entity via dedicated endpoint, ...) if the ID is
     * already in the URI, and we don't need any other data.
     */
    protected function isEmptyBodyAllowed(string $data, array $context): bool
    {
        if (!empty($data)) {
            return false;
        }

        if ($context['operation'] instanceof HttpOperation) {
            $extraProperties = $context['operation']->getExtraProperties();
            if ($extraProperties['allowEmptyBody'] ?? false) {
                return true;
            }
        }

        return false;
    }
}
