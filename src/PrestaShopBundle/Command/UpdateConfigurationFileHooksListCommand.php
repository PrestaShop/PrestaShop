<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerDebugCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * This command is used for listing the hook names in the configuration file.
 */
class UpdateConfigurationFileHooksListCommand extends ContainerDebugCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prestashop:update:configuration-file-hooks-listing')
            ->setDescription('Updates configuration file hooks list')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require $this->getContainer()->get('kernel')->getRootDir() . '/../config/config.inc.php';

        $containerBuilder = $this->getContainerBuilder();
        $serviceDefinitions = $containerBuilder->getDefinitions();
        $gridDefinitionServiceIds = $this->getFilteredServicesDefinitions($serviceDefinitions);
        $hookNames = $this->getHookNamesFromGridDefinitionService($gridDefinitionServiceIds, $containerBuilder);



    }

    /**
     * @param Definition[] $services
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function getFilteredServicesDefinitions(array $services)
    {
        $definitionStartsWith = 'prestashop.core.grid.definition';

        $filteredServiceIds = [];
        foreach ($services as $serviceKey => $service) {
            if ($service->isAbstract()  || $service->isPrivate()) {
                continue;
            }

            if (strpos($serviceKey, $definitionStartsWith) === 0) {
                $filteredServiceIds[] = $serviceKey;
            }
        }

        return $filteredServiceIds;
    }

    /**
     * @param string[] $gridDefinitionServiceIds
     * @param ContainerBuilder $container
     *
     * @return array
     *
     * @throws \Exception
     */
    private function getHookNamesFromGridDefinitionService(array $gridDefinitionServiceIds, ContainerBuilder $container)
    {
        $hookStartsWith = 'action';
        $hookEndsWith = 'GridDefinitionModifier';

        $hookNames = [];
        foreach ($gridDefinitionServiceIds as $serviceId) {
            /** @var GridDefinitionFactoryInterface $service */
            $service = $container->get($serviceId);

            $definition = $service->getDefinition();

            $definitionId = $definition->getId();

            $hookName = $hookStartsWith . Container::camelize($definitionId) . $hookEndsWith;
            $hookNames[] = $hookName;
        }

        return $hookNames;
    }
}
