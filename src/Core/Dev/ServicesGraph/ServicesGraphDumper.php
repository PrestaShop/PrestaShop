<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Dev\ServicesGraph;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

class ServicesGraphDumper
{
    /**
     * We explore the service graph recursively, however
     * we limit the nested search to avoid out-of-memory errors
     */
    const MAX_NESTED_SERVICES_SEARCH_DEPTH = 50;

    /**
     * Used in order to pass $containerBuilder between recursive function calls
     * and to avoid nested arguments stacking
     *
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var string
     */
    private $prestashopBundleRootDirectory;

    /**
     * @param string $prestashopBundleRootDirectory
     */
    public function __construct($prestashopBundleRootDirectory)
    {
        $this->prestashopBundleRootDirectory = $prestashopBundleRootDirectory;
    }

    /**
     * Build service graph for given Symfony controller
     *
     * @param string $realpath
     *
     * @return string graph in DOT format
     *
     * @throws \Exception
     */
    public function buildAndDumpGraphForController($realpath)
    {
        if (false === file_exists($realpath)) {
            throw new \Exception('Bad controller filepath (file does not exist): ' . $realpath);
        }

        $serviceIds = $this->extractServiceIdsFromControllerClass($realpath);
        $serviceContainer = $this->buildServiceContainerFromRootNodes($serviceIds);
        $graph = $this->dumpServiceContainer($serviceContainer);

        return $graph;
    }

    /**
     * Parse the controller file looking for $this->get('...') statements
     * and extract service IDs
     *
     * @param string $controllerFilepath
     *
     * @return string[] service IDs found in given Controller
     *
     * @todo: use Controller FQDN instead of filepath
     */
    protected function extractServiceIdsFromControllerClass($controllerFilepath)
    {
        $fileContent = file_get_contents($controllerFilepath);

        $outputArray = [];
        $result = preg_match_all('#\$this->get\(\\\'(.*)\\\'\)#', $fileContent, $outputArray);

        if (false === $result) {
            throw new \RuntimeException('No service ids found in given controller');
        }

        $ids = $outputArray[1];

        return $ids;
    }

    /**
     * From root nodes, build a Service Container with all the children
     * (recursive search)
     *
     * @param string[] $rootNodes service IDS
     *
     * @return ContainerBuilder
     */
    protected function buildServiceContainerFromRootNodes(array $rootNodes)
    {
        $containerBuilder = $this->getPrestaShopContainerBuilder();
        $this->containerBuilder = $containerBuilder;

        // we're going to add to this 2nd container builder the relevant services
        // extracted from PrestaShop container builder
        $filteredContainer = new ContainerBuilder();

        // we tag all services to be extracted
        foreach ($rootNodes as $serviceToExplore) {
            $rootDefinition = $containerBuilder->getDefinition($serviceToExplore);
            $this->tagDefinitionRecursivelyForGraphExploration($rootDefinition);
        }

        // we extract the tagged services
        foreach ($containerBuilder->getDefinitions() as $id => $def) {
            if ($def->hasTag('to-be-graphed')) {
                $filteredContainer->setDefinition($id, $def);
            }
        }

        return $filteredContainer;
    }

    /**
     * @param ContainerBuilder $serviceContainer
     *
     * @return string
     */
    protected function dumpServiceContainer(ContainerBuilder $serviceContainer)
    {
        $dumper = new CustomGraphvizDumper($serviceContainer);
        $graph = $dumper->dump();

        return $graph;
    }

    /**
     * Load the Symfony container with PrestaShop services
     *
     * @return ContainerBuilder
     *
     * @todo: be able to target PrestaShop services defined outside of PrestaShopBundle
     */
    protected function getPrestaShopContainerBuilder()
    {
        $container = new ContainerBuilder();

        $loader = new YamlFileLoader($container, new FileLocator($this->prestashopBundleRootDirectory . '/Resources/config'));
        $loader->load('services.yml');

        return $container;
    }

    /**
     * @param Definition $definition
     * @param int $i
     */
    protected function tagDefinitionRecursivelyForGraphExploration(Definition $definition, $i = 0)
    {
        $i++;
        $definition->addTag('to-be-graphed');

        foreach ($definition->getArguments() as $argument) {
            $this->tagDefinitionArgumentRecursively($argument, $i);
        }
    }

    /**
     * @param mixed $argument service definition argument
     * @param int $i
     */
    protected function tagDefinitionArgumentRecursively($argument, $i)
    {
        if ($i > self::MAX_NESTED_SERVICES_SEARCH_DEPTH) {
            throw new \RuntimeException('MAX_NESTED_SERVICES_DEFINITION_DEPTH reached, aborting...');
        }

        if ((is_string($argument)) || (is_array($argument))) {
            return;
        }
        if (($argument instanceof Parameter) || ($argument instanceof \Symfony\Component\ExpressionLanguage\Expression)) {
            // @todo: to be improved: this could be part of the graph
            return;
        }

        if ($argument instanceof Reference) {
            $serviceId = (string)$argument;
            try {
                $definition = $this->containerBuilder->getDefinition($serviceId);
            } catch (ServiceNotFoundException $e) {
                // @todo: to be improved: this could be part of the graph
                return;
            }
            $this->tagDefinitionRecursivelyForGraphExploration($definition, $i);
        } else {
            $definition = $argument;
        }

        $definition->addTag('to-be-graphed');
    }
}
