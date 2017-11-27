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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @version  Release: $Revision: 6844 $
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

require_once 'Exception/BuildException.php';
require_once 'Library/ReleaseCreator.php';

$releaseOptions = [
    'version' => [
        'required' => true,
        'description' => 'Desired release version of PrestaShop',
        'longopt' => 'version:'
    ],
    'no-zip' => [
        'description' => 'Do not zip the release directory. Default: false.',
        'longopt' => 'no-zip'
    ],
    'destination-dir' => [
        'description' => 'Path where the release will be store. Default: tools/build/releases/prestashop_{version}',
        'longopt' => 'destination-dir::'
    ],
    'no-installer' => [
        'required' => false,
        'description' => 'Do not put the installer in the release. Interesting if release will be upload remotly by FTP or for public release. Default: false.',
        'longopt' => 'no-installer'
    ],
    'help' => [
        'description' => 'Show help',
        'opt' => 'h',
        'longopt' => 'help'
    ]
];
$helpMessage = "Usage: php {prestashop_root_path}/tools/build/CreateRelease.php --version=<version> [options]\n\n";
$helpMessage .= "Available options are:\n\n";

foreach ($releaseOptions as $optionName => $option) {
    $required = isset($option['required']) ? var_export($option['required'], true) : 'false';
    $description = isset($releaseOptions[$optionName]['description']) ? $releaseOptions[$optionName]['description'] : '';
    $padding = str_pad('', 24, ' ', STR_PAD_LEFT);
    $requiredLabel = str_pad('required:', 13);
    $descriptionLabel = str_pad('description:', 13);
    $optionName = str_pad($optionName, 16);
    $helpMessage .= "\e[32m--$optionName\e[0m\t{$requiredLabel}{$required},\n{$padding}{$descriptionLabel}{$description}\n";
}
$helpMessage .= "\n";
$userOptions = getopt(implode('', array_column($releaseOptions, 'opt')), array_column($releaseOptions, 'longopt'));

// Show help and exit
if (isset($userOptions['h'])
    || isset($userOptions['help'])
    || empty($userOptions)
) {
    echo $helpMessage;

    exit(0);
}

foreach ($releaseOptions as $optionName => $option) {
    $required = isset($option['required']) ? $option['required'] : false;

    if ($required && empty($userOptions[$optionName])) {
        echo "\e[31mERROR:\n";
        echo "'--{$optionName}' option missing.\n";
        echo "-h for help\e[0m\n";

        exit(1);
    }
}
$version = $userOptions['version'];
$destinationDir = '';
$useZip = $useInstaller = true;

if (isset($userOptions['no-zip'])) {
    $useZip = false;
}

if (isset($userOptions['destination-dir'])) {
    $destinationDir = $userOptions['destination-dir'];
}

if (isset($userOptions['no-installer'])) {
    $useInstaller = false;
}

try {
    $releaseCreator = new ReleaseCreator($version, $useInstaller, $useZip, $destinationDir);
    $releaseCreator->createRelease();
} catch (Exception $e) {
    echo "\e[31mERROR:\n";
    echo "Can not create the release.\n";
    echo "{$e->getMessage()}\e[0m\n";

    exit(1);
}

exit(0);