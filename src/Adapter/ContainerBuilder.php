<?php
/**
 * 2007-2017 PrestaShop.
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter;

use Symfony\Component\DependencyInjection\ContainerBuilder as SfContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Config\FileLocator;
use LegacyCompilerPass;

/**
 * Build the Container for PrestaShop Legacy.
 */
class ContainerBuilder
{
    /**
     * @param string $name
     * @param bool $isDebug
     *
     * @return SfContainerBuilder
     *
     * @throws \Exception
     */
    public static function getContainer($name, $isDebug)
    {
        $containerName = ucfirst($name) . 'Container';
        $file = _PS_CACHE_DIR_ . "${containerName}.php";

        if (!$isDebug && file_exists($file)) {
            require_once $file;

            return new $containerName();
        }

        $container = new SfContainerBuilder();
        $container->addCompilerPass(new LegacyCompilerPass());
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $env = $isDebug ? 'dev' : 'prod';
        $servicesPath = _PS_CONFIG_DIR_ . "services/${name}/services_${env}.yml";
        $loader->load($servicesPath);
        $container->compile();

        $dumper = new PhpDumper($container);
        file_put_contents($file, $dumper->dump(array('class' => $containerName)));

        return $container;
    }
}
