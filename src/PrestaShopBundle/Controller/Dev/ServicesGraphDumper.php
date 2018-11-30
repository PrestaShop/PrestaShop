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

namespace PrestaShopBundle\Controller\Dev;

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
     * We explore the service graph recursively
     */
    const MAX_NESTED_SERVICES_DEFINITION_DEPTH = 50;

    /**
     * Used in order to pass $containerBuilder between recursive function calls
     * and to avoid nested arguments stacking
     *
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @param string $realpath
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getGraph($realpath)
    {
        if (false === file_exists($realpath)) {
            throw new \Exception('Bad controller filepath (file does not exist): ' . $realpath);
        }

        $serviceIds = $this->extractServiceIdsFromControllerClass($realpath);
        $graph = $this->exploreController($serviceIds);

        return $graph;
    }

    /**
     * Parse the controller file looking for $this->get('...') statements
     *
     * @param string $controllerFilepath
     *
     * @return string[]
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
     * @param string[] $servicesToExplore
     *
     * @return string
     */
    protected function exploreController(array $servicesToExplore)
    {
        $containerBuilder = $this->getPrestaShopContainerBuilder();
        $this->containerBuilder = $containerBuilder;

        foreach ($servicesToExplore as $serviceToExplore) {
            $rootDefinition = $containerBuilder->getDefinition($serviceToExplore);
            $this->tagDefinitionRecursivelyForGraphExploration($rootDefinition);
        }

        $filteredContainer = new ContainerBuilder();

        foreach ($containerBuilder->getDefinitions() as $id => $def) {
            if ($def->hasTag('to-be-graphed')) {
                $filteredContainer->setDefinition($id, $def);
            }
        }

        $filteredContainer->removeDefinition('service_container');

        $dumper = new CustomGraphvizDumper($filteredContainer);
        $graph = $dumper->dump();

        return $graph;
    }

    /**
     * Load the Symfony container with PrestaShop services
     *
     * @return ContainerBuilder
     */
    protected function getPrestaShopContainerBuilder()
    {
        $container = new ContainerBuilder();

        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/../Resources/config'));
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
        if ($i > self::MAX_NESTED_SERVICES_DEFINITION_DEPTH) {
            throw new \RuntimeException('MAX_NESTED_SERVICES_DEFINITION_DEPTH reached, aborting...');
        }

        if ((is_string($argument)) || (is_array($argument))) {
            return;
        }
        if (($argument instanceof Parameter) || ($argument instanceof \Symfony\Component\ExpressionLanguage\Expression)) {
            return;
        }

        if ($argument instanceof Reference) {
            $serviceId = (string)$argument;
            try {
                $definition = $this->containerBuilder->getDefinition($serviceId);
            } catch (ServiceNotFoundException $e) {
                return;
            }
            $this->tagDefinitionRecursivelyForGraphExploration($definition, $i);
        } else {
            $definition = $argument;
        }

        $definition->addTag('to-be-graphed');
    }
}
