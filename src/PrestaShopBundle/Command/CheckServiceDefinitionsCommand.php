<?php
/**
 * 2007-2019 PrestaShop.
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
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CheckServiceDefinitionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('prestashop:debug:services-container')
            ->setDescription('Looks for issues in services');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $containerBuilder = $this->getPrestaShopContainerBuilder();
        $serviceIds = $containerBuilder->getServiceIds();
        $errors = [];

        // @todo: fix those excluded services
        $excludedIds = [
            'prestashop.admin.import.form_data_provider' // fix is in-progress (known failure)
        ];

        foreach ($serviceIds as $serviceId) {

            if (in_array($serviceId, $excludedIds)) {
                continue;
            }

            $serviceDefinition = $containerBuilder->getDefinition($serviceId);

            if ($serviceDefinition->hasTag('web-only')) {
                // web-only services cannot be used by Symfony CLI
                // @todo: reduce the number of web-only services as much as possible
                continue;
            }

            try {
                var_dump($serviceId);
                $call = $this->getContainer()->get($serviceId);
            } catch (\Exception $e) {
                $errors[] = $e;
            }
        }

        var_dump($errors);
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

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        return $container;
    }
}
