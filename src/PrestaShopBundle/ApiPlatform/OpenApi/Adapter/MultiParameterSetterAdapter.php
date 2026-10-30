<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\Operation;
use ArrayObject;
use DateTimeInterface;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateImmutable;
use PrestaShopBundle\ApiPlatform\DomainObjectDetector;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

/**
 * Adapts multi-parameter setters in OpenAPI schema.
 * Some CQRS commands rely on multi-parameters setters, this is usually done to force specifying related parameters
 * all together because only one is not enough. For such setters we expect the method parameters to be provided in
 * a sub object, so this adapter transforms the schema to match this expected sub object.
 *
 * Example:
 *   UpdateProductCommand::setRedirectOption(string $redirectType, int $redirectTarget)
 *      => expected input ['redirectOption' => ['redirectType' => '301-category', 'redirectTarget' => 42]]
 *
 * The parameters without default value are required in the sub object, and the sub object itself accepts null when
 * the setter can be called with null alone (e.g. setMinimumAmount(null) removes the minimum amount).
 */
class MultiParameterSetterAdapter implements OpenApiSchemaAdapterInterface
{
    public function __construct(
        protected readonly ClassMetadataFactoryInterface $classMetadataFactory,
        protected readonly DomainObjectDetector $domainObjectDetector
    ) {
    }

    public function adapt(string $class, ArrayObject $definition, ?Operation $operation = null): void
    {
        if (!$operation) {
            return;
        }

        $operationClass = ($operation->getInput()['class'] ?? null) ?: $operation->getClass();
        if (!class_exists($operationClass) || !$this->classMetadataFactory->hasMetadataFor($operationClass) || !$this->domainObjectDetector->isDomainObject($operationClass)) {
            return;
        }

        $operationClassMetadata = $this->classMetadataFactory->getMetadataFor($operationClass);
        $operationReflectionClass = $operationClassMetadata->getReflectionClass();
        $methodsWithMultipleArguments = $this->findMethodsWithMultipleArguments($operationReflectionClass);
        if (empty($methodsWithMultipleArguments)) {
            return;
        }

        foreach ($methodsWithMultipleArguments as $methodPropertyName => $setterMethod) {
            /** @var ArrayObject<string, mixed> $methodSchema */
            $methodSchema = new ArrayObject([
                'type' => 'object',
                'properties' => [
                ],
            ]);
            $requiredParameters = [];
            foreach ($setterMethod->getParameters() as $methodParameter) {
                if (!$methodParameter->getType() instanceof ReflectionNamedType) {
                    continue 2;
                }

                if ($this->isDateTime($methodParameter->getType())) {
                    $parameterTypeName = $methodParameter->getType()->getName();
                    $isDateImmutable = ($parameterTypeName === DateImmutable::class || is_subclass_of($parameterTypeName, DateImmutable::class));
                    $format = $isDateImmutable ? 'date' : 'date-time';
                    $methodParameterSchema = new ArrayObject([
                        'format' => $format,
                        'type' => $methodParameter->getType()->allowsNull() ? ['string', 'null'] : 'string',
                    ]);
                    if ($isDateImmutable) {
                        $methodParameterSchema['example'] = '2025-11-05';
                    } else {
                        $methodParameterSchema['example'] = '2025-11-05 16:02:06';
                    }
                } elseif ($this->isDecimalNumber($methodParameter->getType())) {
                    $methodParameterSchema = new ArrayObject([
                        'type' => $methodParameter->getType()->allowsNull() ? ['number', 'null'] : 'number',
                        'example' => 42.99,
                    ]);
                } elseif ($methodParameter->getType()->isBuiltin()) {
                    $schemaType = $this->getSchemaType($methodParameter->getType()->getName());
                    $methodParameterSchema = new ArrayObject([
                        'type' => $methodParameter->getType()->allowsNull() ? [$schemaType, 'null'] : $schemaType,
                    ]);
                } else {
                    continue 2;
                }
                $methodSchema['properties'][$methodParameter->getName()] = $methodParameterSchema;
                if (!$methodParameter->isOptional()) {
                    $requiredParameters[] = $methodParameter->getName();
                }
            }
            if (!empty($requiredParameters)) {
                $methodSchema['required'] = $requiredParameters;
            }
            // A null sub object is set like any property, by calling the setter with null as its only argument
            if ($setterMethod->getNumberOfRequiredParameters() <= 1 && $setterMethod->getParameters()[0]->allowsNull()) {
                $methodSchema['type'] = ['object', 'null'];
            }

            foreach (array_keys($methodSchema['properties']) as $propertyName) {
                unset($definition['properties'][$propertyName]);
            }
            $definition['properties'][$methodPropertyName] = $methodSchema;
        }
    }

    protected function getSchemaType(string $builtInType): string
    {
        return match ($builtInType) {
            'int' => 'integer',
            'float' => 'number',
            'bool' => 'boolean',
            default => $builtInType,
        };
    }

    protected function isDecimalNumber(ReflectionNamedType $methodParameter): bool
    {
        return $methodParameter->getName() === DecimalNumber::class || is_subclass_of($methodParameter->getName(), DecimalNumber::class);
    }

    protected function isDateTime(ReflectionNamedType $methodParameter): bool
    {
        if (!class_exists($methodParameter->getName()) && !interface_exists($methodParameter->getName())) {
            return false;
        }

        $implements = class_implements($methodParameter->getName());
        if (empty($implements)) {
            return false;
        }

        return in_array(DateTimeInterface::class, $implements);
    }

    /**
     * @return array<string, ReflectionMethod>
     */
    protected function findMethodsWithMultipleArguments(ReflectionClass $reflectionClass): array
    {
        $methodsWithMultipleArguments = [];
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if (
                $reflectionMethod->getNumberOfParameters() <= 1
                || $reflectionMethod->isStatic()
                || $reflectionMethod->isConstructor()
                || $reflectionMethod->isDestructor()
            ) {
                continue;
            }

            if (str_starts_with($reflectionMethod->getName(), 'set')) {
                $methodPropertyName = lcfirst(substr($reflectionMethod->getName(), 3));
            } elseif (str_starts_with($reflectionMethod->getName(), 'with')) {
                $methodPropertyName = lcfirst(substr($reflectionMethod->getName(), 4));
            } else {
                $methodPropertyName = $reflectionMethod->getName();
            }
            $methodsWithMultipleArguments[$methodPropertyName] = $reflectionMethod;
        }

        return $methodsWithMultipleArguments;
    }
}
