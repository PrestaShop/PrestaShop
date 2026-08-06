#!/usr/bin/env php
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

use PrestaShop\PrestaShop\Core\Version;
use Symfony\Component\Console\Output\ConsoleOutput;

define('_PS_ROOT_DIR_', dirname(__DIR__, 2));
const _PS_IN_TEST_ = true;
const __PS_BASE_URI__ = '/';
const _PS_MODULE_DIR_ = _PS_ROOT_DIR_ . '/tests/Resources/modules/';
const _PS_ALL_THEMES_DIR_ = _PS_ROOT_DIR_ . '/tests/Resources/themes/';
require_once _PS_ROOT_DIR_ . '/install-dev/init.php';

$output = new ConsoleOutput();
$exitCode = 0;

$dbName = _DB_NAME_;
$tmpDir = sys_get_temp_dir();
$globalDump = sprintf('%s/ps_dump_%s_%s.sql', $tmpDir, $dbName, Version::VERSION);
$tableDumpPattern = sprintf('%s/ps_dump_%s_%s_*.sql', $tmpDir, $dbName, Version::VERSION);

// 1. Global dump file
if (file_exists($globalDump)) {
    $output->writeln(sprintf('<info> ✓ Global dump present:</info> %s', $globalDump));
} else {
    $output->writeln(sprintf('<error> ✗ Global dump missing:</error> %s', $globalDump));
    $exitCode = 1;
}

// 2. Per-table dump files (the global pattern also matches the global dump itself, so we exclude it)
$tableDumps = array_filter(glob($tableDumpPattern) ?: [], static fn (string $f): bool => $f !== $globalDump);
$dumpCount = null;
if (!empty($tableDumps)) {
    $dumpCount = count($tableDumps);
    $output->writeln(sprintf('<info> ✓ Per-table dumps present:</info> %d files', $dumpCount));
} else {
    $output->writeln(sprintf('<error> ✗ No per-table dumps found at:</error> %s', $tableDumpPattern));
    $exitCode = 1;
}

// 3. Test database created and accessible
$tableCount = null;
try {
    $tables = Db::getInstance()->executeS('SHOW TABLES');
    if (is_array($tables) && count($tables) > 0) {
        $tableCount = count($tables);
        $output->writeln(sprintf('<info> ✓ Test database "%s" accessible:</info> %d tables', $dbName, $tableCount));
    } else {
        $output->writeln(sprintf('<error> ✗ Test database "%s" exists but has no tables</error>', $dbName));
        $exitCode = 1;
    }
} catch (Throwable $e) {
    $output->writeln(sprintf('<error> ✗ Cannot access test database "%s":</error> %s', $dbName, $e->getMessage()));
    $exitCode = 1;
}

// 4. Per-table dumps and DB tables count must match (content not checked)
if ($dumpCount !== null && $tableCount !== null) {
    if ($dumpCount === $tableCount) {
        $output->writeln(sprintf('<info> ✓ Table counts match:</info> %d dumps = %d tables', $dumpCount, $tableCount));
    } else {
        $output->writeln(sprintf('<error> ✗ Table count mismatch:</error> %d per-table dumps vs %d tables in the DB', $dumpCount, $tableCount));
        $exitCode = 1;
    }
}

$output->writeln('');
if ($exitCode === 0) {
    $output->writeln('<info>Test environment is ready.</info>');
} else {
    $output->writeln('<comment>Run `composer create-test-db` to provision the test database.</comment>');
}

exit($exitCode);
