<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Ai;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Runs .ai/bin/check-conventions.php on a throwaway git repository: the script reads the
 * repository it sits in, so it is copied into the fixture.
 */
class CheckConventionsTest extends TestCase
{
    private const SCRIPT = __DIR__ . '/../../../.ai/bin/check-conventions.php';

    private string $repository;

    protected function setUp(): void
    {
        $this->repository = sys_get_temp_dir() . '/check-conventions-' . bin2hex(random_bytes(6));
        $filesystem = new Filesystem();
        $filesystem->copy(self::SCRIPT, $this->repository . '/.ai/bin/check-conventions.php');
        $filesystem->dumpFile($this->repository . '/.ai/CONTEXT.md', "# Root\n");
        $filesystem->dumpFile(
            $this->repository . '/.ai/Domain/Product/CONTEXT.md',
            "# Product\n\n## Layers\n\n| Layer | Path |\n|---|---|\n| Core | `src/Core/Domain/Product/` |\n\n## Related\n"
        );
        $this->git(['init', '--quiet', '--initial-branch=base']);
        $this->commit();
        $this->git(['switch', '--quiet', '-c', 'work']);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->repository);
    }

    public function testPlaintextListsTheContextsThenTheFindings(): void
    {
        $this->addFiles(['src/Core/Domain/Product/ProductThing.php']);

        $process = $this->checkConventions(['base', '--plaintext']);

        $output = $process->getOutput();
        $this->assertStringStartsWith('Read these .ai/ files before reviewing', $output);
        $this->assertStringContainsString("\n- .ai/CONTEXT.md", $output);
        $this->assertStringContainsString("\n- .ai/Domain/Product/CONTEXT.md (1 file(s): src/Core/Domain/Product/ProductThing.php)", $output);
        $finding = '- FAIL  src/Core/Domain/Product/ProductThing.php: new PHP file without declare(strict_types=1)';
        $this->assertGreaterThan(strpos($output, '.ai/Domain/Product/CONTEXT.md'), strpos($output, $finding));
        $this->assertDoesNotMatchRegularExpression('/^#|\e\[/m', $output);
        $this->assertSame(1, $process->getExitCode());
    }

    public function testPlaintextFitsInOneGithubComment(): void
    {
        $files = [];
        for ($i = 0; $i < 600; ++$i) {
            $files[] = sprintf('src/Core/Domain/Product/%s/File%03d.php', str_repeat('VeryLongFolderName', 5), $i);
        }
        $this->addFiles($files);

        $process = $this->checkConventions(['base', '--plaintext']);

        $output = $process->getOutput();
        $this->assertLessThan(65536, strlen($output));
        $this->assertMatchesRegularExpression('/^- \(\+\d+ more\)$/m', $output);
        $this->assertSame(1, $process->getExitCode());
    }

    public function testJsonAndPlaintextAreExclusive(): void
    {
        $process = $this->checkConventions(['base', '--json', '--plaintext']);

        $this->assertSame(2, $process->getExitCode());
        $this->assertSame('', $process->getOutput());
        $this->assertStringContainsString('--json and --plaintext are exclusive', $process->getErrorOutput());
    }

    /**
     * @param string[] $paths
     */
    private function addFiles(array $paths): void
    {
        $filesystem = new Filesystem();
        foreach ($paths as $path) {
            $filesystem->dumpFile($this->repository . '/' . $path, "<?php\n\nclass " . basename($path, '.php') . "\n{\n}\n");
        }
        $this->commit();
    }

    private function commit(): void
    {
        $this->git(['add', '--all']);
        $this->git(['-c', 'user.name=Test', '-c', 'user.email=test@example.com', 'commit', '--quiet', '-m', 'fixture']);
    }

    /**
     * @param string[] $args
     */
    private function git(array $args): void
    {
        (new Process(array_merge(['git'], $args), $this->repository))->mustRun();
    }

    /**
     * @param string[] $args
     */
    private function checkConventions(array $args): Process
    {
        $process = new Process(array_merge([PHP_BINARY, '.ai/bin/check-conventions.php'], $args), $this->repository);
        $process->run();

        return $process;
    }
}
