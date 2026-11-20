<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use DirectoryIterator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

/**
 * Command to check and repair public bundle symlinks.
 *
 * Symfony creates symbolic links under {admin}/bundles/ (and public/bundles/) that
 * point into vendor/ so that bundle-provided assets (JS/CSS) are served as static
 * files. Those links are created by "assets:install" during composer install and
 * are not refreshed by cache clearing. When the installation directory is moved
 * or renamed (for example when a staging domain becomes the production one),
 * the links keep pointing to the old absolute path and the bundle assets 404.
 *
 * Usage:
 *   bin/console prestashop:assets:symlinks           # detect only (exit 1 if stale)
 *   bin/console prestashop:assets:symlinks --fix     # rewrite stale links (relative)
 *   bin/console prestashop:assets:symlinks --fix -v  # verbose per-link output
 */
#[AsCommand(
    name: 'prestashop:assets:symlinks',
    description: 'Check (and optionally repair) public bundle symlinks in the admin folder'
)]
class AssetsSymlinksCommand extends Command
{
    public function __construct(
        private readonly string $adminDir,
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'fix',
            null,
            InputOption::VALUE_NONE,
            'Rewrite stale symlinks as relative links pointing into vendor/'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fix = (bool) $input->getOption('fix');

        $bundleDirs = array_filter([
            $this->adminDir . '/bundles',
            $this->projectDir . '/public/bundles',
        ], 'is_dir');

        if ($bundleDirs === []) {
            $io->warning('No bundle folder found. Nothing to check.');

            return Command::SUCCESS;
        }

        $filesystem = new Filesystem();
        $vendorDir = $this->projectDir . '/vendor';
        $staleCount = 0;
        $fixedCount = 0;
        $failedCount = 0;

        foreach ($bundleDirs as $bundleDir) {
            $io->section(sprintf('Scanning %s', $bundleDir));

            foreach (new DirectoryIterator($bundleDir) as $entry) {
                if ($entry->isDot() || !$entry->isLink()) {
                    continue;
                }

                $linkPath = $entry->getPathname();
                $currentTarget = readlink($linkPath) ?: '';
                $resolvedTarget = $entry->getRealPath();

                $isStale = $resolvedTarget === false || !file_exists($resolvedTarget);
                $isOutsideProject = $resolvedTarget !== false && !str_starts_with($resolvedTarget, $vendorDir);

                if (!$isStale && !$isOutsideProject) {
                    if ($output->isVerbose()) {
                        $io->writeln(sprintf('  <info>OK</info>   %s', $entry->getFilename()));
                    }
                    continue;
                }

                ++$staleCount;

                $expectedTarget = $this->guessExpectedTarget($currentTarget, $vendorDir);
                if ($expectedTarget === null || !is_dir($expectedTarget)) {
                    $io->writeln(sprintf(
                        '  <error>UNKNOWN</error> %s -> %s (cannot guess vendor target)',
                        $entry->getFilename(),
                        $currentTarget
                    ));
                    ++$failedCount;
                    continue;
                }

                $io->writeln(sprintf(
                    '  <comment>STALE</comment> %s -> %s',
                    $entry->getFilename(),
                    $currentTarget
                ));

                if (!$fix) {
                    continue;
                }

                try {
                    $filesystem->remove($linkPath);
                    $relative = rtrim($filesystem->makePathRelative($expectedTarget, $bundleDir), '/');
                    $filesystem->symlink($relative, $linkPath);
                    $io->writeln(sprintf('    <info>fixed</info> -> %s', $relative));
                    ++$fixedCount;
                } catch (Throwable $e) {
                    $io->writeln(sprintf('    <error>fix failed:</error> %s', $e->getMessage()));
                    ++$failedCount;
                }
            }
        }

        $io->newLine();

        if ($staleCount === 0) {
            $io->success('All bundle symlinks are healthy.');

            return Command::SUCCESS;
        }

        if (!$fix) {
            $io->warning(sprintf('%d stale symlink(s) found. Run with --fix to repair.', $staleCount));

            return Command::FAILURE;
        }

        if ($failedCount > 0) {
            $io->error(sprintf('%d/%d symlink(s) fixed, %d failed.', $fixedCount, $staleCount, $failedCount));

            return Command::FAILURE;
        }

        $io->success(sprintf('%d symlink(s) repaired.', $fixedCount));

        return Command::SUCCESS;
    }

    /**
     * Extract the "vendor/..." suffix from the current (possibly broken) link
     * target and rebuild it against the current vendor directory.
     */
    private function guessExpectedTarget(string $currentTarget, string $vendorDir): ?string
    {
        $marker = '/vendor/';
        $pos = strrpos($currentTarget, $marker);
        if ($pos === false) {
            return null;
        }

        $suffix = substr($currentTarget, $pos + strlen($marker));

        return $vendorDir . '/' . $suffix;
    }
}
