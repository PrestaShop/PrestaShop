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
