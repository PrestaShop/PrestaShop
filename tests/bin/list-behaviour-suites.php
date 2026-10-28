<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

/*
 * Lists the Behat suites of a configuration file, from the most scenarios to the least.
 */

use Symfony\Component\Yaml\Yaml;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

$configFile = $argv[1];
$basePath = dirname($configFile);
$scenarios = [];

foreach (Yaml::parseFile($configFile)['default']['suites'] as $suite => $configuration) {
    $scenarios[$suite] = 0;
    foreach ($configuration['paths'] ?? [] as $path) {
        $path = str_replace('%paths.base%', $basePath, $path);
        $files = is_dir($path)
            ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS))
            : [new SplFileInfo($path)];
        foreach ($files as $file) {
            if ($file->getExtension() === 'feature') {
                $scenarios[$suite] += preg_match_all('/^\s*Scenario/m', (string) file_get_contents($file->getPathname()));
            }
        }
    }
}

arsort($scenarios);
echo implode(PHP_EOL, array_keys($scenarios)), PHP_EOL;
