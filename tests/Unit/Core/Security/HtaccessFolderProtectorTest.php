<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Security;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Security\HtaccessFolderProtector;

class HtaccessFolderProtectorTest extends TestCase
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
        new HtaccessFolderProtector($invalidPath);
    }

    /**
     * @dataProvider getInvalidFolders
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\FileNotFoundException
     */
    public function testProtectInvalidFolders($invalidFolder)
    {
        $protector = new HtaccessFolderProtector($this->htaccessTemplatePath);
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

        $protector = new HtaccessFolderProtector($this->htaccessTemplatePath);

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

        $protector = new HtaccessFolderProtector($this->htaccessTemplatePath);

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
