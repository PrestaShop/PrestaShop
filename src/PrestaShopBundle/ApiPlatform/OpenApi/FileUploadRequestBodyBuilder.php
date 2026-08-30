<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\OpenApi;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\OpenApi\Model\MediaType;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ArrayObject;
use PrestaShopBundle\ApiPlatform\Encoder\MultipartDecoder;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use SplFileInfo;

/**
 * Documents uploaded file properties for operations based on multipart/form-data input.
 *
 * The input schema of CQRS write operations is generated from the CQRS command class, which
 * only contains the uploaded file path as a string (e.g. AddProductImageCommand::$filePath),
 * so the file itself is absent from the generated schema and Swagger UI displays no file input.
 * The API resource class is the one that declares the uploaded file as a SplFileInfo/File
 * property, so this builder injects those properties into the request body schema of multipart
 * operations as `type: string, format: binary`, which is the OpenAPI way of describing a binary
 * file part.
 *
 * The request body schema is inlined (dereferenced) on purpose: a component schema can be shared
 * by several operations based on the same CQRS command (e.g. module upload by source URL or by
 * archive file), so the file properties must only be documented on the multipart operation, not
 * on the shared schema.
 */
class FileUploadRequestBodyBuilder
{
    public const MULTIPART_CONTENT_TYPE = 'multipart/form-data';

    protected const COMPONENT_SCHEMA_PREFIX = '#/components/schemas/';

    /**
     * Returns the public properties of the operation's API resource typed as SplFileInfo (or a
     * child class like Symfony's File), mapped to whether they accept null (optional upload).
     * Returns an empty array for operations that don't accept multipart/form-data input.
     *
     * @return array<string, bool>
     */
    public function getUploadedFileProperties(HttpOperation $operation): array
    {
        if (!array_key_exists(MultipartDecoder::FORMAT, $operation->getInputFormats() ?? [])
            || !class_exists($operation->getClass())
        ) {
            return [];
        }

        $fileProperties = [];
        $reflectionClass = new ReflectionClass($operation->getClass());
        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic() || !$property->getType() instanceof ReflectionNamedType) {
                continue;
            }

            $propertyType = $property->getType()->getName();
            if ($propertyType === SplFileInfo::class || is_subclass_of($propertyType, SplFileInfo::class)) {
                $fileProperties[$property->getName()] = $property->getType()->allowsNull();
            }
        }

        return $fileProperties;
    }

    /**
     * Replaces the multipart/form-data request body schema of the OpenAPI operation by an inline
     * schema containing the original properties plus the uploaded file properties.
     *
     * @param ArrayObject $componentSchemas The components.schemas of the OpenAPI documentation, used to dereference the original schema
     * @param array<string, bool> $uploadedFileProperties Uploaded file property names mapped to whether they accept null
     */
    public function adaptOperation(OpenApiOperation $openApiOperation, ArrayObject $componentSchemas, array $uploadedFileProperties): OpenApiOperation
    {
        $requestBody = $openApiOperation->getRequestBody();
        if (empty($uploadedFileProperties)
            || null === $requestBody
            || null === $requestBody->getContent()
            || !$requestBody->getContent()->offsetExists(self::MULTIPART_CONTENT_TYPE)
        ) {
            return $openApiOperation;
        }

        /** @var MediaType $mediaType */
        $mediaType = $requestBody->getContent()->offsetGet(self::MULTIPART_CONTENT_TYPE);
        $schema = $this->dereferenceSchema($mediaType->getSchema(), $componentSchemas);

        $properties = (array) ($schema['properties'] ?? []);
        $required = (array) ($schema['required'] ?? []);
        foreach ($uploadedFileProperties as $propertyName => $allowsNull) {
            $properties[$propertyName] = new ArrayObject([
                'type' => 'string',
                'format' => 'binary',
            ]);
            if (!$allowsNull && !in_array($propertyName, $required, true)) {
                $required[] = $propertyName;
            }
        }

        $inlineSchema = new ArrayObject([
            'type' => 'object',
            'properties' => $properties,
        ]);
        if (!empty($required)) {
            $inlineSchema['required'] = array_values($required);
        }

        $content = clone $requestBody->getContent();
        $content->offsetSet(self::MULTIPART_CONTENT_TYPE, $mediaType->withSchema($inlineSchema));

        return $openApiOperation->withRequestBody($requestBody->withContent($content));
    }

    /**
     * Resolves a `$ref` schema pointing to components.schemas so that the file properties can be
     * merged with the schema's own properties in the final inline schema.
     */
    protected function dereferenceSchema(?ArrayObject $schema, ArrayObject $componentSchemas): ArrayObject
    {
        if (null === $schema) {
            return new ArrayObject();
        }

        if (!empty($schema['$ref']) && str_starts_with($schema['$ref'], self::COMPONENT_SCHEMA_PREFIX)) {
            $schemaName = substr($schema['$ref'], strlen(self::COMPONENT_SCHEMA_PREFIX));
            if ($componentSchemas->offsetExists($schemaName)) {
                return $componentSchemas->offsetGet($schemaName);
            }
        }

        return $schema;
    }
}
