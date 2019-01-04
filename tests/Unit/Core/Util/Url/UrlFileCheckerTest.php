<?php
/**
 * 2007-2019 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Util\Url;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\Url\UrlFileChecker;
use PrestaShop\PrestaShop\Core\Util\Url\UrlFileCheckerInterface;

class UrlFileCheckerTest extends TestCase
{
    protected function setUp()
    {
        touch(realpath(__DIR__) . '/not_writable_files/.htaccess');
        touch(realpath(__DIR__) . '/not_writable_files/robots.txt');
        chmod(realpath(__DIR__) . '/not_writable_files/.htaccess', 111);
        chmod(realpath(__DIR__) . '/not_writable_files/robots.txt', 111);

        touch(realpath(__DIR__) . '/writable_files/.htaccess');
        touch(realpath(__DIR__) . '/writable_files/robots.txt');
        chmod(realpath(__DIR__) . '/writable_files/.htaccess', 755);
        chmod(realpath(__DIR__) . '/writable_files/robots.txt', 755);
    }

    protected function tearDown()
    {
        unlink(realpath(__DIR__) . '/not_writable_files/.htaccess');
        unlink(realpath(__DIR__) . '/not_writable_files/robots.txt');

        unlink(realpath(__DIR__) . '/writable_files/.htaccess');
        unlink(realpath(__DIR__) . '/writable_files/robots.txt');
    }

    public function testIsValidImplementation()
    {
        $checker = new UrlFileChecker(realpath(__DIR__));

        $this->assertInstanceOf(UrlFileCheckerInterface::class, $checker);
    }

    public function testNotWritableFiles()
    {
        $checker = new UrlFileChecker(realpath(__DIR__ . '/not_writable_files'));

        $this->assertFalse($checker->isHtaccessFileWritable());
        $this->assertFalse($checker->isRobotsFileWritable());
    }

    public function testWritableFiles()
    {
        $checker = new UrlFileChecker(realpath(__DIR__ . '/writable_files'));

        $this->assertTrue($checker->isHtaccessFileWritable());
        $this->assertTrue($checker->isRobotsFileWritable());
    }
}
