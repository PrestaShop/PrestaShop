<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace Tests\Unit\Core\Security;

use PHPUnit\Framework\TestCase;
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

    protected function setUp()
    {
        parent::setUp();

        $this->htaccessTemplate = file_get_contents($this->htaccessTemplatePath);
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\FileNotFoundException
     */
    public function testInvalidTemplatePath()
    {
        $invalidPath = __DIR__ . '/../../Resources/security/invalid_htaccess';
        new HtaccessFolderGuard($invalidPath);
    }

    /**
     * @dataProvider getInvalidFolders
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\FileNotFoundException
     */
    public function testProtectInvalidFolders($invalidFolder)
    {
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
