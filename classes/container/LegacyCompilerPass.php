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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use PrestaShop\PrestaShop\Adapter\DependencyInjection\FileLoader\YamlByTagFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Component\Finder\Finder;

class LegacyCompilerPass implements CompilerPassInterface
{
    /**
     * Add legacy services that need to be built using Context::getContext().
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $context = Context::getContext();

        $this->buildSyntheticDefinitions([
            'configuration',
            'context',
            'db',
            'shop',
            'employee',
        ], $container);

        $container->set('context', $context);
        $container->set('configuration', new Configuration());
        $container->set('db', Db::getInstance());
        $container->set('shop', $context->shop);
        $container->set('employee', $context->employee);

        $this->loadServicesFromActiveModules($container);
    }

    private function buildSyntheticDefinitions(array $keys, ContainerBuilder $container)
    {
        foreach ($keys as $key) {
            $definition = new Definition();
            $definition->setSynthetic(true);
            $container->setDefinition($key, $definition);
        }
    }

    private function loadServicesFromActiveModules(ContainerBuilder $container)
    {
        $queryResult = Db::getInstance()->executeS('SELECT name from ' . _DB_PREFIX_ . 'module WHERE active = 1');
        $activeModules = array_map('current', $queryResult);

        foreach ($this->getModulesPaths() as $modulePath) {
            if (in_array($modulePath->getFilename(), $activeModules)
                && file_exists($modulePath . '/config/services.yml')
            ) {
                $loader = new YamlByTagFileLoader($container, new FileLocator($modulePath . '/config/'));
                $loader->load('services.yml');
            }
        }
    }

    /**
     * @return \Iterator
     */
    private function getModulesPaths()
    {
        return Finder::create()->directories()->in(__DIR__ . '/../../modules')->depth(0);
    }
}
