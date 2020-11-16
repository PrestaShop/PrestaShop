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

namespace PrestaShop\PrestaShop\Core\CommandBus\Parser;

use ReflectionClass;
use ReflectionMethod;

class CommandHandlerDefinitionParser
{
    public const HANDLER_METHOD_NAME = 'handle';
    public const RETURN_TAG = '@return';

    /**
     * @param string $handlerClass
     * @param string $commandClass
     *
     * @return CommandHandlerDefinition
     */
    public function parseDefinition(string $handlerClass, string $commandClass): CommandHandlerDefinition
    {
        $commandReflection = new ReflectionClass($commandClass);
        $handlerReflection = new ReflectionClass($handlerClass);

        return new CommandHandlerDefinition(
            $this->parseType($commandClass),
            $handlerClass,
            $commandClass,
            $this->parseCommandConstructorParams($commandReflection),
            $this->parseDescription($commandReflection),
            $this->parseReturnType($handlerReflection),
            $handlerReflection->getInterfaceNames()
        );
    }

    /**
     * @param ReflectionClass $command
     *
     * @return string[]
     */
    private function parseCommandConstructorParams(ReflectionClass $command): array
    {
        if (!$constructor = $command->getConstructor()) {
            return [];
        }

        $params = [];
        foreach ($constructor->getParameters() as $parameter) {
            $param = sprintf('%s', $parameter->getName());

            if ($parameter->getType()) {
                $param = sprintf('%s %s', $parameter->getType()->getName(), $param);
            }

            if ($parameter->isOptional()) {
                $parameter->allowsNull() ? $param = sprintf('?%s', $param) : null;
                $param = sprintf('%s = %s', $param, var_export($parameter->getDefaultValue(), true));
            }
            $params[] = $param;
        }

        return $params;
    }

    /**
     * Parses return type from docblock
     *
     * @param ReflectionClass $handlerReflection
     *
     * @return string
     */
    private function parseReturnType(ReflectionClass $handlerReflection): ?string
    {
        $method = $handlerReflection->getMethod(self::HANDLER_METHOD_NAME);

        foreach ($handlerReflection->getInterfaces() as $interface) {
            if ($interface->hasMethod(self::HANDLER_METHOD_NAME)) {
                $method = $interface->getMethod(self::HANDLER_METHOD_NAME);
                break;
            }
        }

        if ($returnType = $this->parseReturnTypeFromDocblock($method)) {
            return $returnType;
        }

        if ($method->hasReturnType()) {
            return $method->getReturnType()->getName();
        }

        return null;
    }

    /**
     * @param ReflectionMethod $method
     *
     * @return string|null
     */
    private function parseReturnTypeFromDocblock(ReflectionMethod $method): ?string
    {
        $docBlock = $method->getDocComment();
        if (!$docBlock) {
            return null;
        }

        $tagPosition = strpos($docBlock, self::RETURN_TAG);

        if (false !== $tagPosition) {
            $returnType = substr($docBlock, $tagPosition, (strpos($docBlock, PHP_EOL, $tagPosition)) - $tagPosition);
            $returnType = str_replace(sprintf('%s ', self::RETURN_TAG), '', $returnType);

            return $returnType;
        }

        return null;
    }

    /**
     * @param ReflectionClass $reflectionClass
     *
     * @return string
     */
    private function parseDescription(ReflectionClass $reflectionClass): string
    {
        if (!$docBlock = $reflectionClass->getDocComment()) {
            return '';
        }

        /**
         * Removes comment symbols, annotations, and line breaks.
         */
        $description = preg_replace("/\/+\*\*|\*+\/|\*|@(\w+)\b(.*)|\n/",
            '',
            $docBlock
        );

        /**
         * Replaces multiple spaces to single space
         */
        $description = preg_replace('/ +/', ' ', $description);

        /*
         * Strips whitespace from the beginning and end
         */
        return trim($description);
    }

    /**
     * Checks whether the command is of type Query or Command by provided name
     *
     * @param string $commandName
     *
     * @return string command|query
     */
    private function parseType($commandName): string
    {
        if (strpos($commandName, '\Command\\')) {
            return CommandHandlerDefinition::TYPE_COMMAND;
        }

        return CommandHandlerDefinition::TYPE_QUERY;
    }
}
