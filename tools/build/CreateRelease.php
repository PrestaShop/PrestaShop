<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
require_once 'Exception/BuildException.php';
require_once 'Library/ReleaseCreator.php';
require_once 'Library/ConsoleWriter.php';
require_once 'Library/Version.php';

$consoleWrite = new ConsoleWriter();
$lineSeparator = PHP_EOL;

if (PHP_SAPI !== 'cli') {
    $consoleWrite->displayText(
        "ERROR:{$lineSeparator}Must be run has a CLI script.{$lineSeparator}",
        ConsoleWriter::COLOR_RED
    );

    exit(1);
}

$releaseOptions = [
    'version' => [
        'description' => 'Desired release version of PrestaShop',
        'longopt' => 'version:',
    ],
    'no-zip' => [
        'description' => 'Do not zip the release directory. Default: false.',
        'longopt' => 'no-zip',
    ],
    'destination-dir' => [
        'description' => 'Path where the release will be store. Default: tools/build/releases/prestashop_{version}',
        'longopt' => 'destination-dir::',
    ],
    'no-installer' => [
        'required' => false,
        'description' => 'Do not put the installer in the release. Interesting if release will be upload remotely by FTP or for public release. Default: false.',
        'longopt' => 'no-installer',
    ],
    'keep-tests' => [
        'description' => 'Keep tests folder in the release. Default: false.',
        'longopt' => 'keep-tests',
    ],
    'distribution' => [
        'description' => 'Distribution type for app/metadata.json (e.g. open_source). When set, metadata file is generated.',
        'longopt' => 'distribution::',
    ],
    'help' => [
        'description' => 'Show help',
        'opt' => 'h',
        'longopt' => 'help',
    ],
];
$helpMessage = "Usage: php {prestashop_root_path}/tools/build/CreateRelease.php [--version=<version>] [options]{$lineSeparator}{$lineSeparator}"
    . "Available options are:{$lineSeparator}{$lineSeparator}";

foreach ($releaseOptions as $optionName => $option) {
    $required = isset($option['required']) ? var_export($option['required'], true) : 'false';
    $description = $releaseOptions[$optionName]['description'];
    $padding = str_pad('', 24, ' ', STR_PAD_LEFT);
    $requiredLabel = str_pad('required:', 13);
    $descriptionLabel = str_pad('description:', 13);
    $optionName = str_pad($optionName, 16);
    $helpMessage .= "\e[32m--$optionName\e[0m\t{$requiredLabel}{$required},{$lineSeparator}{$padding}{$descriptionLabel}{$description}{$lineSeparator}";
}
$helpMessage .= "{$lineSeparator}";
$userOptions = getopt(implode('', array_column($releaseOptions, 'opt')), array_column($releaseOptions, 'longopt'));

// Show help and exit
if (isset($userOptions['h'])
    || isset($userOptions['help'])
) {
    echo $helpMessage;

    exit(0);
}

$destinationDir = '';
$useZip = $useInstaller = true;
$keepTests = false;
$distribution = '';

if (isset($userOptions['version'])) {
    $version = $userOptions['version'];
} else {
    $version = null;
}

if (isset($userOptions['no-zip'])) {
    $useZip = false;
}

if (isset($userOptions['destination-dir'])) {
    $destinationDir = $userOptions['destination-dir'];
}

if (isset($userOptions['no-installer'])) {
    $useInstaller = false;
}

if (isset($userOptions['keep-tests'])) {
    $keepTests = true;
}

if (isset($userOptions['distribution'])) {
    $distribution = $userOptions['distribution'];
}

try {
    $releaseCreator = new ReleaseCreator($version, $useInstaller, $useZip, $destinationDir, $keepTests, $distribution);
    $releaseCreator->createRelease();
} catch (Exception $e) {
    $consoleWrite->displayText(
        "ERROR:{$lineSeparator}Can not create the release.{$lineSeparator}Message: '{$e->getMessage()}'{$lineSeparator}",
        ConsoleWriter::COLOR_RED
    );

    exit(1);
}

exit(0);
