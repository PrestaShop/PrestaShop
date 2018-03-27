<?php
/**
 * 2007-2018 PrestaShop
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

use PrestaShopBundle\Install\Upgrade;

$parametersFilepath = __DIR__  . '/parameters.php';
if (!file_exists($parametersFilepath)) {
    // let's check first if there's some old config files which could be migrated
    if (Upgrade::migrateSettingsFile() === false) {
        // nothing to migrate ? return
        return;
    }
}

$parameters = require($parametersFilepath);

if (!array_key_exists('parameters', $parameters)) {
    throw new \Exception('Missing "parameters" key in "parameters.php" configuration file');
}

if (isset($_SERVER['argv'])) {
    $input = new \Symfony\Component\Console\Input\ArgvInput();
    $env = $input->getParameterOption(['--env', '-e'], getenv('SYMFONY_ENV') ?: 'dev');

    if ($env === 'test' && !defined('_PS_IN_TEST_')) {
        define('_PS_IN_TEST_', 1);
    }
}

foreach ($parameters['parameters'] as $key => $value) {
    if (defined('_PS_IN_TEST_') && $key === 'database_name') {
        $value = 'test_'.$value;
    }
    $container->setParameter($key, $value);
}
