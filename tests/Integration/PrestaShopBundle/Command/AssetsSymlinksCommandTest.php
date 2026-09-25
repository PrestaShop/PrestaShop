<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Command;

use DirectoryIterator;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class AssetsSymlinksCommandTest extends KernelTestCase
{
    private const COMMAND = 'prestashop:assets:symlinks';

    private string $bundleDir;

    /** @var array<string, string> Original target for each link we may touch, keyed by link basename */
    private array $originalTargets = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $adminDir = self::getContainer()->getParameter('prestashop.admin_dir');
        $this->bundleDir = $adminDir . '/bundles';

        // Snapshot the current state so we can restore it whatever the test does.
        foreach (new DirectoryIterator($this->bundleDir) as $entry) {
            if (!$entry->isDot() && $entry->isLink()) {
                $this->originalTargets[$entry->getFilename()] = readlink($entry->getPathname());
            }
        }
    }

    protected function tearDown(): void
    {
        // Restore every link exactly as it was, whatever the test left behind.
        foreach ($this->originalTargets as $name => $target) {
            $path = $this->bundleDir . '/' . $name;
            if (file_exists($path) || is_link($path)) {
                @unlink($path);
            }
            @symlink($target, $path);
        }

        parent::tearDown();
    }

    public function testDetectsStaleSymlink(): void
    {
        $this->breakOneLink();

        $tester = $this->runCommand([]);

        $this->assertSame(Command::FAILURE, $tester->getStatusCode(), 'Stale link should produce a non-zero exit code');
        $this->assertStringContainsString('STALE', $tester->getDisplay());
        $this->assertStringContainsString('Run with --fix', $tester->getDisplay());
    }

    public function testFixRewritesStaleSymlinkAsRelative(): void
    {
        [$linkName, $originalTarget] = $this->breakOneLink();

        $tester = $this->runCommand(['--fix' => true]);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode(), 'Fixed link should exit 0');
        $this->assertStringContainsString('repaired', $tester->getDisplay());

        $newTarget = readlink($this->bundleDir . '/' . $linkName);
        $this->assertNotFalse($newTarget);
        $this->assertStringStartsWith('..', $newTarget, 'Repaired link should be relative');

        $resolved = realpath($this->bundleDir . '/' . $linkName);
        $this->assertNotFalse($resolved, 'Repaired link should point to an existing directory');
        $this->assertSame(realpath($originalTarget), $resolved, 'Repaired link should resolve to the original vendor asset dir');
    }

    public function testNoopWhenAllHealthy(): void
    {
        $tester = $this->runCommand([]);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $this->assertStringContainsString('healthy', $tester->getDisplay());
    }

    /**
     * @return array{0: string, 1: string} [linkName, originalAbsoluteTargetForAssertions]
     */
    private function breakOneLink(): array
    {
        $linkName = (string) array_key_first($this->originalTargets);
        $originalTarget = $this->originalTargets[$linkName];

        $linkPath = $this->bundleDir . '/' . $linkName;
        @unlink($linkPath);

        // Point somewhere that doesn't exist but still looks like a former vendor path,
        // so the command can guess the expected target from the "vendor/..." suffix.
        $fakeOldPath = '/nonexistent/former-install/vendor/' . $this->vendorSuffixOf($originalTarget);
        symlink($fakeOldPath, $linkPath);

        return [$linkName, $originalTarget];
    }

    private function vendorSuffixOf(string $target): string
    {
        $pos = strrpos($target, '/vendor/');

        return $pos === false ? basename($target) : substr($target, $pos + strlen('/vendor/'));
    }

    /**
     * @param array<string, mixed> $options
     */
    private function runCommand(array $options): CommandTester
    {
        $application = new Application(self::$kernel);
        $command = $application->find(self::COMMAND);
        $tester = new CommandTester($command);
        $tester->execute(['command' => self::COMMAND] + $options);

        return $tester;
    }
}
