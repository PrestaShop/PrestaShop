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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Foundation\IoC;

use ReflectionClass;
use ReflectionNamedType;

class Container
{
    private $bindings = [];
    private $instances = [];
    private $namespaceAliases = [];

    public function knows($serviceName)
    {
        return array_key_exists($serviceName, $this->bindings);
    }

    private function knowsNamespaceAlias($alias)
    {
        return array_key_exists($alias, $this->namespaceAliases);
    }

    public function bind($serviceName, $constructor, $shared = false)
    {
        if ($this->knows($serviceName)) {
            throw new Exception(sprintf('Cannot bind `%s` again. A service name can only be bound once.', $serviceName));
        }

        $this->bindings[$serviceName] = [
            'constructor' => $constructor,
            'shared' => $shared,
        ];

        return $this;
    }

    public function aliasNamespace($alias, $namespacePrefix)
    {
        if ($this->knowsNamespaceAlias($alias)) {
            throw new Exception(sprintf('Namespace alias `%1$s` already exists and points to `%2$s`', $alias, $this->namespaceAliases[$alias]));
        }

        $this->namespaceAliases[$alias] = $namespacePrefix;

        return $this;
    }

    public function resolveClassName($className)
    {
        $colonPos = strpos($className, ':');
        if (0 !== $colonPos && false !== $colonPos) {
            $alias = substr($className, 0, $colonPos);
            if ($this->knowsNamespaceAlias($alias)) {
                $class = ltrim(substr($className, $colonPos + 1), '\\');

                return $this->namespaceAliases[$alias] . '\\' . $class;
            }
        }

        return $className;
    }

    private function makeInstanceFromClassName($className, array $alreadySeen)
    {
        $className = $this->resolveClassName($className);

        try {
            $refl = new ReflectionClass($className);
        } catch (\ReflectionException $re) {
            throw new Exception(sprintf('This doesn\'t seem to be a class name: `%s`.', $className));
        }

        $args = [];

        if ($refl->isAbstract()) {
            throw new Exception(sprintf('Cannot build abstract class: `%s`.', $className));
        }

        $classConstructor = $refl->getConstructor();

        if ($classConstructor) {
            foreach ($classConstructor->getParameters() as $param) {
                if ($param->getType() instanceof ReflectionNamedType && !$param->getType()->isBuiltin()) {
                    $args[] = $this->doMake($param->getType()->getName(), $alreadySeen);
                } elseif ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                } else {
                    throw new Exception(sprintf('Cannot build a `%s`.', $className));
                }
            }
        }

        if (count($args) > 0) {
            return $refl->newInstanceArgs($args);
        } else {
            // newInstanceArgs with empty array fails in PHP 5.3 when the class
            // doesn't have an explicitly defined constructor
            return $refl->newInstance();
        }
    }

    private function doMake($serviceName, array $alreadySeen = [])
    {
        if (array_key_exists($serviceName, $alreadySeen)) {
            throw new Exception(sprintf('Cyclic dependency detected while building `%s`.', $serviceName));
        }

        $alreadySeen[$serviceName] = true;

        if (!$this->knows($serviceName)) {
            $this->bind($serviceName, $serviceName);
        }

        $binding = $this->bindings[$serviceName];

        if ($binding['shared'] && array_key_exists($serviceName, $this->instances)) {
            return $this->instances[$serviceName];
        } else {
            $constructor = $binding['constructor'];

            if (is_callable($constructor)) {
                $service = call_user_func($constructor);
            } elseif (!is_string($constructor)) {
                // user already provided the value, no need to construct it.
                $service = $constructor;
            } else {
                // assume the $constructor is a class name
                $service = $this->makeInstanceFromClassName($constructor, $alreadySeen);
            }

            if ($binding['shared']) {
                $this->instances[$serviceName] = $service;
            }

            return $service;
        }
    }

    public function make($serviceName)
    {
        return $this->doMake($serviceName, []);
    }
}
