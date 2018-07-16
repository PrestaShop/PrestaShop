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

require_once 'Library/ConsoleWriter.php';
require_once 'Library/ReleaseChecker.php';
require_once 'Library/VersionNumber.php';

$consoleWrite = new ConsoleWriter();
$lineSeparator = PHP_EOL;

if (php_sapi_name() !== 'cli') {
    $consoleWrite->displayText(
        "ERROR:{$lineSeparator}Must be run has a CLI script.{$lineSeparator}",
        ConsoleWriter::COLOR_RED
    );

    exit(1);
}

$releaseOptions = [
    'version' => [
        'required' => true,
        'description' => 'Desired release version of PrestaShop',
        'longopt' => 'version:'
    ],
    'help' => [
        'description' => 'Show help',
        'opt' => 'h',
        'longopt' => 'help'
    ]
];
$helpMessage = "Usage: php {prestashop_root_path}/tools/build/CheckGitBranch.php --version=<version>";
$userOptions = getopt(implode('', array_column($releaseOptions, 'opt')), array_column($releaseOptions, 'longopt'));

// Show help and exit
if (isset($userOptions['h'])
    || isset($userOptions['help'])
    || empty($userOptions)
) {
    echo $helpMessage;

    exit(0);
}

$version = $userOptions['version'];

try {
    $releaseChecker = new ReleaseChecker($version);
    $releaseChecker->checkRelease();
} catch (Exception $e) {
    $consoleWrite->displayText(
        "ERROR:{$lineSeparator}Can not check release.{$lineSeparator}Message: '{$e->getMessage()}'{$lineSeparator}",
        ConsoleWriter::COLOR_RED
    );

    exit(1);
}

exit(0);
