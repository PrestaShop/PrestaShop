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


namespace PrestaShop\PrestaShop\Core\Composer;

use Composer\Script\Event;

final class ModuleHandler
{
    public static function install(Event $event)
    {
        $composer = $event->getComposer();
        $rootPath = realpath($composer->getConfig()->get('vendor-dir'). '/..');

        $extras = $composer->getPackage()->getExtra();

        if (!isset($extras['prestashop'])) {
            throw new \InvalidArgumentException('The parameter handler needs to be configured through the extra.prestashop-modules setting.');
        }

        $configs = $extras['prestashop'];

        if (!is_array($configs)) {
            throw new \InvalidArgumentException('The extra.prestashop setting must be an array or a configuration object.');
        }

        if (array_keys($configs) !== range(0, count($configs) - 1)) {
            $configs = array($configs);
        }

        $processor = new ModulesConfigurationProcessor($event->getIO());
        foreach ($configs as $config) {
            if (!is_array($config)) {
                throw new \InvalidArgumentException('The extra.prestashop setting must be an array of configuration objects.');
            }

            $processor->processInstallation($config, $rootPath);
        }
    }

    public static function update(Event $event)
    {
        $composer = $event->getComposer();
        $rootPath = realpath($composer->getConfig()->get('vendor-dir'). '/..');

        $extras = $composer->getPackage()->getExtra();

        if (!isset($extras['prestashop'])) {
            throw new \InvalidArgumentException('The parameter handler needs to be configured through the extra.prestashop-modules setting.');
        }

        $config = $extras['prestashop'];

        if (!is_array($config)) {
            throw new \InvalidArgumentException('The extra.prestashop setting must be an array or a configuration object.');
        }

        $processor = new ModulesConfigurationProcessor($event->getIO());
        $processor->processUpdate($config, $rootPath);
    }
}
