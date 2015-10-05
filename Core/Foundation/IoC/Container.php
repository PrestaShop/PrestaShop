<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Foundation\IoC;

/**
 * The application container. Used to retrieve/locate services, and instantiate them.
 * This container class is a transitive system before deeper Symfony2 integration.
 *
 * The container can hold already instantiated services to avoid new instances. If a required service
 * already exists, then the instance is returned, else it instantiate it and resolve its dependencies.
 *
 * Some alias exists but can only be called into make(), not bind()!
 * In the bind() method you MUST indicate either:
 * - a full namespaced class name (to allow injection of its instance into other services)
 * - a 'final:' prefixed service name to avoid injection. In this case the service is only accessible through explicit make() call, not through dependency injection.
 *
 * To have a full list of available services and final instances, please see:
 * @see Core_Business_ContainerBuilder
 */
class Container
{
    private $bindings = array();
    private $instances = array();
    private $namespaceAliases = array();

    public function knows($serviceName)
    {
        return array_key_exists($serviceName, $this->bindings);
    }

    final private function knowsNamespaceAlias($alias)
    {
        return array_key_exists($alias, $this->namespaceAliases);
    }

    public function bind($serviceName, $constructor, $shared = false, array $privateInjections = array())
    {
        if ($this->knows($serviceName)) {
            throw new Exception(
                sprintf('Cannot bind `%s` again. A service name can only be bound once.', $serviceName)
            );
        }

        $this->bindings[$serviceName] = array(
            'constructor' => $constructor,
            'shared' => $shared,
            'private_injections' => $privateInjections
        );

        return $this;
    }

    public function aliasNamespace($alias, $namespacePrefix)
    {
        if ($this->knowsNamespaceAlias($alias)) {
            throw new Exception(
                sprintf(
                    'Namespace alias `%1$s` already exists and points to `%2$s`',
                    $alias, $this->namespaceAliases[$alias]
                )
            );
        }

        $this->namespaceAliases[$alias] = $namespacePrefix;
        return $this;
    }

    public function resolveClassName($className)
    {
        $colonPos = strpos($className, ':');
        if (0 !== $colonPos) {
            $alias = substr($className, 0, $colonPos);
            if ($alias == 'final') {
                throw new Exception(sprintf('This final service is unknown: `%s`.', $className));
            }
            if ($this->knowsNamespaceAlias($alias)) {
                $class = ltrim(substr($className, $colonPos + 1), '\\');
                return $this->namespaceAliases[$alias] . '\\' . $class;
            }
        }
        return $className;
    }

    final private function makeInstanceFromClassName($className, array $alreadySeen, array $privateInjections = array())
    {
        $className = $this->resolveClassName($className);

        try {
            $refl = new \ReflectionClass($className);
        } catch (\ReflectionException $re) {
            throw new Exception(sprintf('This doesn\'t seem to be a class name: `%s`.', $className));
        }

        $args = array();

        if ($refl->isAbstract()) {
            throw new Exception(sprintf('Cannot build abstract class: `%s`.', $className));
        }

        $classConstructor = $refl->getConstructor();

        if ($classConstructor) {
            foreach ($classConstructor->getParameters() as $param) {
                $paramClass = $param->getClass();
                if ($paramClass) {
                    foreach ($privateInjections as $privateInjection) {
                        if (get_class($privateInjection) == $param->getClass()->getName()) {
                            $args[] = $privateInjection;
                            continue 2;
                        }
                    }
                    $args[] = $this->doMake($param->getClass()->getName(), $alreadySeen);
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

    final private function doMake($serviceName, array $alreadySeen = array())
    {
        if (array_key_exists($serviceName, $alreadySeen)) {
            throw new Exception(sprintf(
                'Cyclic dependency detected while building `%s`.',
                $serviceName
            ));
        }

        $alreadySeen[$serviceName] = true;

        if (!$this->knows($serviceName)) {
            $serviceNameAlias = $this->resolveClassName($serviceName);
            if (!$this->knows($serviceNameAlias)) {
                $this->bind($serviceName, $serviceName);
            } else {
                $serviceName = $serviceNameAlias;
            }
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
                $service = $this->makeInstanceFromClassName($constructor, $alreadySeen, $binding['private_injections']?:array());
            }

            if ($binding['shared']) {
                $this->instances[$serviceName] = $service;
            }

            return $service;
        }
    }

    public function make($serviceName)
    {
        return $this->doMake($serviceName, array());
    }
}
