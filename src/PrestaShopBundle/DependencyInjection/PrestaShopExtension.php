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

namespace PrestaShopBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Adds main PrestaShop core services to the Symfony container.
 */
class PrestaShopExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $env = $container->getParameter('kernel.environment');
        $loader->load('services_' . $env . '.yml');

        $container->setParameter('prestashop.translator.class', $config['translator']['class']);
        $container->setParameter('prestashop.translator.data_collector', $config['translator']['data_collector']);

        $container->setParameter('prestashop.cache_dir', $config['directories']['cache_dir']);
        $container->setParameter('prestashop.mode_dev', $config['directories']['mode_dev']);
        $container->setParameter('prestashop.employee_img_dir', $config['directories']['employee_img_dir']);
        $container->setParameter('prestashop.profile_img_dir', $config['directories']['profile_img_dir']);
        $container->setParameter('prestashop.product_img_dir', $config['directories']['product_img_dir']);
        $container->setParameter('prestashop.img_dir', $config['directories']['img_dir']);
        $container->setParameter('prestashop.tmp_img_dir', $config['directories']['tmp_img_dir']);
        $container->setParameter('prestashop.module_dir', $config['directories']['module_dir']);
        $container->setParameter('prestashop.download_dir', $config['directories']['download_dir']);
        $container->setParameter('prestashop.genders_dir', $config['directories']['genders_dir']);
        $container->setParameter('prestashop.root_dir', $config['directories']['root_dir']);
        $container->setParameter('prestashop.pdf_dir', $config['directories']['pdf_dir']);
        $container->setParameter('prestashop.admin_profile', $config['directories']['admin_profile']);

        // @deprecated since 8.1, will be removed in 9.0
        $container->setParameter('ps_cache_dir', $config['directories']['cache_dir']);
        $container->setParameter('prestashop.addons.categories', $config['addons']['categories']);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'prestashop';
    }
}
