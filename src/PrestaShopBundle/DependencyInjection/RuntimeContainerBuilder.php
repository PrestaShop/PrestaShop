<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
declare(strict_types=1);

namespace PrestaShopBundle\DependencyInjection;

use Exception;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ExpressionLanguage;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * Class RuntimeContainerBuilder allows to dynamically add services to a built
 * (and running) container. It wraps the running container and copies its parameters.
 * It can then be used by any loader, e.g:
 *
 *  $runtimeBuilder = new RuntimeContainerBuilder($this->container);
 *  $loader = new YamlFileLoader($runtimeBuilder, new FileLocator($serviceConfigPath));
 *  $loader->load('services.yml');
 *  $runtimeBuilder->compile();
 *
 * Then during the compilation process it sets all the required services needed in the loaded
 * configuration from the wrapped container, and once the compilation has ended it sets all the
 * new services into the initial container.
 */
class RuntimeContainerBuilder extends ContainerBuilder
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ExpressionLanguage
     */
    protected $expressionLanguage;

    /**
     * @var array
     */
    protected $dependenciesConfigPaths;

    /**
     * @param ContainerInterface $container
     * @param array $dependenciesConfigPaths
     */
    public function __construct(
        ContainerInterface $container,
        array $dependenciesConfigPaths
    ) {
        parent::__construct();
        $this->container = $container;
        $this->dependenciesConfigPaths = $dependenciesConfigPaths;
    }

    /**
     * {@inheritdoc}
     */
    public function compile()
    {
        $this->loadDependencies();

        foreach ($this->getDefinitions() as $definition) {
            foreach ($definition->getArguments() as $argumentValue) {
                if ($argumentValue instanceof Reference) {
                    $this->injectService((string) $argumentValue);
                } elseif ($argumentValue instanceof Expression) {
                    $this->getExpressionLanguage()->compile((string) $argumentValue, ['this' => 'container']);
                } elseif (is_string($argumentValue)) {
                    $this->parseParameter($argumentValue);
                }
            }
        }
        foreach ($this->getAliases() as $aliasName => $alias) {
            $aliasId = (string) $alias;
            $this->injectService($aliasId);
            $this->injectService($aliasName);
        }
        foreach ($this->getParameterBag()->all() as $parameter => $value) {
            $this->injectParameter($parameter);
        }

        parent::compile(false);

        foreach ($this->getServiceIds() as $id) {
            if (!$this->container->has($id)) {
                $this->container->set($id, $this->get($id));
            }
        }
        foreach ($this->getAliases() as $alias) {
            $aliasId = (string) $alias;
            if (!$this->container->has($aliasId)) {
                $this->container->set($aliasId, $this->get($alias));
            }
        }
    }

    /**
     * Unfortunately we need to load some dependent services, or we would lack the parent definitions because
     * abstract services like form.type.translatable.aware are not findable in a compiled container. So we use
     * a special loader that only adds the required definitions (this keeps this container lighter).
     *
     * @throws Exception
     */
    private function loadDependencies()
    {
        foreach ($this->dependenciesConfigPaths as $configPath) {
            if (!file_exists($configPath . '/services.yml')) {
                continue;
            }

            $dependencyLoader = new DependencyYamlFileLoader($this, new FileLocator($configPath));
            foreach ($this->getDefinitions() as $definition) {
                if ($definition instanceof ChildDefinition) {
                    $dependencyLoader->addDependency($definition->getParent());
                }
            }
            $dependencyLoader->load($configPath . '/services.yml');
        }
    }

    /**
     * Parse parameter value in case it contains reference to other parameters
     *
     * @param string $parameterValue
     */
    private function parseParameter(string $parameterValue): void
    {
        if (preg_match_all('/%(.*)%/', $parameterValue, $matches)) {
            foreach ($matches[1] as $parameterValue) {
                $this->injectParameter($parameterValue);
            }
        }
    }

    /**
     * Inject parameters in current container and try to parse its value
     *
     * @param string $parameterName
     */
    private function injectParameter(string $parameterName): void
    {
        if ($this->container->hasParameter($parameterName)) {
            $parameterValue = $this->container->getParameter($parameterName);
            $this->setParameter($parameterName, $parameterValue);

            // In case the parameter contains other parameters
            if (is_string($parameterValue)) {
                $this->parseParameter($parameterValue);
            }
        }
    }

    /**
     * Inject a service from the running container into this builder
     *
     * @param string $id
     *
     * @throws Exception
     */
    private function injectService(string $id)
    {
        if ('service_container' === $id || !$this->container->has($id)) {
            return;
        }

        $this->set($id, $this->container->get($id));
    }

    /**
     * This code was inspired by the AnalyzeServiceReferencesPass
     *
     * @return ExpressionLanguage|null
     */
    private function getExpressionLanguage()
    {
        if (null === $this->expressionLanguage) {
            if (!class_exists(ExpressionLanguage::class)) {
                throw new RuntimeException('Unable to use expressions as the Symfony ExpressionLanguage component is not installed.');
            }

            $this->expressionLanguage = new ExpressionLanguage(
                null,
                $this->getExpressionLanguageProviders(),
                function ($arg) {
                    if ('""' === substr_replace($arg, '', 1, -1)) {
                        $id = stripcslashes(substr($arg, 1, -1));
                        $this->injectService($id);
                    }

                    return sprintf('$this->get(%s)', $arg);
                }
            );
        }

        return $this->expressionLanguage;
    }
}
