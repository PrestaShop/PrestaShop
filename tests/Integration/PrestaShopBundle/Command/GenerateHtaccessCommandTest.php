<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateHtaccessCommandTest extends KernelTestCase
{
    private string $htaccessPath;

    private ?string $htaccessBackup = null;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        // Back up the existing .htaccess so the test does not corrupt the shared file
        $this->htaccessPath = _PS_ROOT_DIR_ . '/.htaccess';
        $this->htaccessBackup = file_exists($this->htaccessPath) ? file_get_contents($this->htaccessPath) : null;
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // Restore the original .htaccess (or remove it if there was none)
        if ($this->htaccessBackup !== null) {
            file_put_contents($this->htaccessPath, $this->htaccessBackup);
        } elseif (file_exists($this->htaccessPath)) {
            unlink($this->htaccessPath);
        }
    }

    public function testGenerateHtaccessFile(): void
    {
        $application = new Application(static::$kernel);
        $command = $application->find('prestashop:htaccess:generate');

        $this->assertNotNull($command, 'Command prestashop:htaccess:generate not found');

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => $command->getName(),
            '--force' => true,
        ]);
        $display = $tester->getDisplay();

        // Check exit code
        $this->assertEquals(0, $tester->getStatusCode(), 'Command did not exit successfully');

        // Check that the command output indicates success
        $this->assertStringContainsString('.htaccess successfully generated', $display);
        $this->assertStringContainsString(_PS_ROOT_DIR_ . '/.htaccess', $display);
    }
}
