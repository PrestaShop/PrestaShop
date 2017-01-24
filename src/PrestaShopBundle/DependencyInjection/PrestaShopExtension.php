<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

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
        $configuration = new AddOnsConfiguration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.yml');

        $hasVerifySslParameter = $container->hasParameter('addons.api_client.verify_ssl');

        if ($hasVerifySslParameter) {
            $verifySsl = $container->getParameter('addons.api_client.verify_ssl');
        } else {
            $verifySsl = $config['addons']['api_client']['verify_ssl'];
        }

        $container->setParameter('prestashop.addons.api_client.verify_ssl', $verifySsl);
        if (!$container->hasParameter('prestashop.addons.api_client.ttl')) {
            $container->setParameter('prestashop.addons.api_client.ttl', $config['addons']['api_client']['ttl']);
        }

        $this->addClassesToCompile(array(
            $container->getDefinition('prestashop.router')->getClass(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'prestashop';
    }
}
