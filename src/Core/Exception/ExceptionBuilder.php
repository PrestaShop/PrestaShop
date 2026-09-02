<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Exception;

use ReflectionClass;
use ReflectionNamedType;
use Throwable;

/**
 * This utility class helps building an exception dynamically based on the exception class that is interpreted via reflection we try
 * and deduced the different parameters and try to inject the proper ones in the proper order.
 */
class ExceptionBuilder
{
    public static function buildException(string $exceptionClass, string $message, int $errorCode = 0, ?Throwable $previousException = null, ?int $objectModelId = null): Throwable
    {
        $reflectionClass = new ReflectionClass($exceptionClass);
        $constructorParameters = $reflectionClass->getConstructor()->getParameters();
        $parameters = [];
        foreach ($constructorParameters as $constructorParameter) {
            $parameterName = $constructorParameter->getName();

            // If parameter name contains message it is probably the message
            if (preg_match('/message/i', $parameterName)) {
                $parameters[] = $message;
                continue;
            }

            // If parameter name finished by code it is probably the error code
            if (preg_match('/code$/i', $parameterName)) {
                $parameters[] = $errorCode;
                continue;
            }

            // One of the parameter is an object ID (it contains id in its name)
            if (null !== $objectModelId && preg_match('/.*id$/i', $parameterName)) {
                if ($constructorParameter->getType() instanceof ReflectionNamedType) {
                    $parameterTypeName = $constructorParameter->getType()->getName();
                    // It can be an integer
                    if ($parameterTypeName === 'int') {
                        $parameters[] = $objectModelId;
                        continue;
                    }

                    // Or it could be a ValueObject instance that we try and build
                    if (class_exists($parameterTypeName)) {
                        $parameters[] = new $parameterTypeName($objectModelId);
                        continue;
                    }
                }
            }

            // If parameter is throwable it is probably the previous exception
            if ($constructorParameter->getType() instanceof ReflectionNamedType) {
                $parameterTypeName = $constructorParameter->getType()->getName();
                if ($parameterTypeName === Throwable::class || is_subclass_of($parameterTypeName, Throwable::class)) {
                    $parameters[] = $previousException;
                    continue;
                }
            }

            // If parameter contains previous (despite not having Throwable type) it is probably the previous exception
            if (preg_match('/previous/i', $parameterName)) {
                $parameters[] = $previousException;
                continue;
            }

            if ($constructorParameter->isDefaultValueAvailable()) {
                $parameters[] = $constructorParameter->getDefaultValue();
                continue;
            }

            throw new InvalidArgumentException(sprintf('Can not prepare parameter %s for class %s', $parameterName, $exceptionClass));
        }

        return $reflectionClass->newInstanceArgs($parameters);
    }
}
