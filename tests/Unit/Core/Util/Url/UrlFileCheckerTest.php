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

namespace Tests\Unit\Core\Util\Url;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\Url\UrlFileChecker;
use PrestaShop\PrestaShop\Core\Util\Url\UrlFileCheckerInterface;

class UrlFileCheckerTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('foo');

        vfsStream::newFile('not_writable_files/.htaccess', 0400)->at($this->root);
        vfsStream::newFile('not_writable_files/robots.txt', 0400)->at($this->root);

        vfsStream::newFile('writable_files/.htaccess', 0644)->at($this->root);
        vfsStream::newFile('writable_files/robots.txt', 0644)->at($this->root);
    }

    public function testIsValidImplementation()
    {
        $checker = new UrlFileChecker($this->root->url());

        $this->assertInstanceOf(UrlFileCheckerInterface::class, $checker);
    }

    public function testNotWritableFiles()
    {
        $checker = new UrlFileChecker($this->root->url() . '/not_writable_files');

        $this->assertFalse($checker->isHtaccessFileWritable());
        $this->assertFalse($checker->isRobotsFileWritable());
    }

    public function testWritableFiles()
    {
        $checker = new UrlFileChecker($this->root->url() . '/writable_files');

        $this->assertTrue($checker->isHtaccessFileWritable());
        $this->assertTrue($checker->isRobotsFileWritable());
    }
}
