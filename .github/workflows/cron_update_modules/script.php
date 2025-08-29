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

declare(strict_types=1);

const VENDOR_NAME = 'prestashop/';

function fetchVersionComposerLock(): array {
    $composerLock = file_get_contents('composer.lock');
    $composerLock = json_decode($composerLock, true);

    $versions = [];
    foreach ($composerLock['packages'] as $package) {
        if (strpos($package['name'], VENDOR_NAME) !== 0) {
            continue;
        }
        $versions[$package['name']] = $package['version'];
    }

    return $versions;
}

// Fetch modules from the repository PrestaShop/prestashop-modules
$hCurl = curl_init();
curl_setopt($hCurl, CURLOPT_USERAGENT,'PrestaShop/CronUpdateModules');
curl_setopt($hCurl, CURLOPT_URL, 'https://api.github.com/repos/PrestaShop/prestashop-modules/git/trees/master');
curl_setopt($hCurl, CURLOPT_RETURNTRANSFER, 1);
$gitJSON = curl_exec($hCurl);
curl_close($hCurl);

if (empty($gitJSON)) {
    echo 'Empty return from the API';
    die();
}
$gitJSON = json_decode($gitJSON, true);
if (empty($gitJSON['tree'])) {
    echo 'Empty return from the API';
    die();
}
$modulesGit = array_filter($gitJSON['tree'], function(array $val) {
    return $val['type'] === 'commit';
});

// Fetch packages from the composer.json
$composerJSON = file_get_contents('composer.json');
if (empty($gitJSON)) {
    echo 'Empty return from composer.json';
    die();
}
$composerJSON = json_decode($composerJSON, true);
if (empty($composerJSON['require'])) {
    echo 'No required packages in composer.json';
    die();
}
$modulesComposer = array_keys($composerJSON['require']);

// Fetch versions from the composer.lock
$moduleComposerLockOriginal = fetchVersionComposerLock();

// Update modules with Composer
foreach ($modulesGit as $moduleGit) {
    if (!in_array(VENDOR_NAME . $moduleGit['path'], $modulesComposer)) {
        continue;
    }
    exec(sprintf(
        'composer update prestashop/%s',
        $moduleGit['path']
    ));
}

// Fetch versions from the composer.lock
$moduleComposerLockAfterUpdate = fetchVersionComposerLock();

// Versions
$pullRequestBodyBumpModules = '';
foreach ($moduleComposerLockOriginal as $moduleName => $moduleVersion) {
    if (!array_key_exists($moduleName, $moduleComposerLockAfterUpdate)) {
        continue;
    }
    if ($moduleVersion === $moduleComposerLockAfterUpdate[$moduleName]) {
        continue;
    }
    $pullRequestBodyBumpModules .= 'Module `' . $moduleName. '`: Bump version from `' . $moduleVersion
        . '` to `' . $moduleComposerLockAfterUpdate[$moduleName] . '`<br />';
}

if (empty($pullRequestBodyBumpModules)) {
    echo '';
    exit();
}

$branch = $argv[1] ?? 'develop';

$prTable = '| Questions         | Answers' . "|\r\n";
$prTable .= '| ----------------- | -------------------------------------------------------' . "|\r\n";
$prTable .= '| Branch?           | ' . $branch . "|\r\n";
$prTable .= '| Description?      | Updated PrestaShop composer packages, details below.' . "|\r\n";
$prTable .= '| Type?             | improvement' . "|\r\n";
$prTable .= '| Category?         | CO' . "|\r\n";
$prTable .= '| BC breaks?        | no' . "|\r\n";
$prTable .= '| Deprecations?     | no' . "|\r\n";
$prTable .= '| Fixed ticket?     | N/A' . "|\r\n";
$prTable .= '| How to test?      | N/A' . "|\r\n";
$prTable .= "\r\n\r\n";
$prTable .= $pullRequestBodyBumpModules;

file_put_contents(
    'cron_php_update_modules.txt',
    $prTable
);
