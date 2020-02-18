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

namespace PrestaShopBundle\DependencyInjection;

use RuntimeException;
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
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container->getParameterBag());
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function compile()
    {
        foreach ($this->getDefinitions() as $definition) {
            foreach ($definition->getArguments() as $argument) {
                if ($argument instanceof Reference) {
                    $this->set((string) $argument, $this->container->get((string) $argument));
                }
                if ($argument instanceof Expression) {
                    $this->getExpressionLanguage()->compile((string) $argument, ['this' => 'container']);
                }
            }
        }

        parent::compile(false);

        foreach ($this->getParameterBag()->all() as $parameter => $value) {
            if (!$this->container->hasParameter($parameter)) {
                $this->container->setParameter($parameter, $value);
            }
        }
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

            $providers = $this->getExpressionLanguageProviders();
            $this->expressionLanguage = new ExpressionLanguage(null, $providers, function ($arg) {
                if ('""' === substr_replace($arg, '', 1, -1)) {
                    $id = stripcslashes(substr($arg, 1, -1));
                    if ($this->container->has($id)) {
                        $this->set($id, $this->container->get($id));
                    }
                }

                return sprintf('$this->get(%s)', $arg);
            });
        }

        return $this->expressionLanguage;
    }
}
