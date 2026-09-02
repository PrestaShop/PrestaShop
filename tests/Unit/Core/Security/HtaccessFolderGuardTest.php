<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Security;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Security\HtaccessFolderGuard;

class HtaccessFolderGuardTest extends TestCase
{
    /**
     * @var string
     */
    private $htaccessTemplatePath = __DIR__ . '/../../Resources/security/htaccess';

    /**
     * @var false|string
     */
    private $htaccessTemplate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->htaccessTemplate = file_get_contents($this->htaccessTemplatePath);
    }

    public function testInvalidTemplatePath()
    {
        $this->expectException(FileNotFoundException::class);

        $invalidPath = __DIR__ . '/../../Resources/security/invalid_htaccess';
        new HtaccessFolderGuard($invalidPath);
    }

    /**
     * @dataProvider getInvalidFolders
     */
    public function testProtectInvalidFolders($invalidFolder)
    {
        $this->expectException(FileNotFoundException::class);

        $protector = new HtaccessFolderGuard($this->htaccessTemplatePath);
        $protector->protectFolder($invalidFolder);
    }

    public function testProtectFolder()
    {
        $testFolder = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'security_htaccess';
        if (!file_exists($testFolder)) {
            mkdir($testFolder);
        }
        $testHtaccessPath = $testFolder . DIRECTORY_SEPARATOR . '.htaccess';
        if (file_exists($testHtaccessPath)) {
            unlink($testHtaccessPath);
        }

        $protector = new HtaccessFolderGuard($this->htaccessTemplatePath);

        $this->assertFalse(file_exists($testHtaccessPath));
        $protector->protectFolder($testFolder);
        $this->assertTrue(file_exists($testHtaccessPath));
        $this->assertEquals($this->htaccessTemplate, file_get_contents($testHtaccessPath));

        unlink($testHtaccessPath);
        rmdir($testFolder);
    }

    public function testExistingHtaccessFile()
    {
        $testFolder = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'security_htaccess';
        if (!file_exists($testFolder)) {
            mkdir($testFolder);
        }
        $testHtaccessPath = $testFolder . DIRECTORY_SEPARATOR . '.htaccess';
        if (file_exists($testHtaccessPath)) {
            unlink($testHtaccessPath);
        }
        $existingContent = 'existing file';
        file_put_contents($testHtaccessPath, $existingContent);

        $protector = new HtaccessFolderGuard($this->htaccessTemplatePath);

        $this->assertTrue(file_exists($testHtaccessPath));
        $protector->protectFolder($testFolder);
        $this->assertTrue(file_exists($testHtaccessPath));
        $this->assertEquals($existingContent, file_get_contents($testHtaccessPath));

        unlink($testHtaccessPath);
        rmdir($testFolder);
    }

    public function getInvalidFolders()
    {
        return [
            [__DIR__ . '/../../Resources/security/not_found_folder'],
            [__DIR__ . '/../../Resources/security/htaccess'],
        ];
    }
}
