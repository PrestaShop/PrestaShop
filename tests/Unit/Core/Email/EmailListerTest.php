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

namespace Tests\Unit\Core\Email;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Email\EmailLister;
use PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem;
use SplFileInfo;

class EmailListerTest extends TestCase
{
    public function testGetAvailableMails(): void
    {
        $dir = './example/dir';
        /** @var MockObject|FileSystem */
        $filesystem = $this->getMockBuilder(FileSystem::class)->getMock();
        $filesystem
            ->expects($this->once())
            ->method('listFilesRecursively')
            ->with($dir)
            ->willReturn([
                new SplFileInfo('./.html'),
                new SplFileInfo('./.team.html'),
                new SplFileInfo('./feature'),
                new SplFileInfo('./sub/cart.HTML'),
                new SplFileInfo('/root/order.html'),
                new SplFileInfo('./repeated/order.html'),
            ]);

        $emailLister = new EmailLister($filesystem);
        $actual = $emailLister->getAvailableMails($dir);
        $expected = ['cart', 'order'];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @param string $mailName
     * @param string $expectedReturn
     *
     * @dataProvider providerGetCleanedMailName
     */
    public function testGetCleanedMailName(string $mailName, string $expectedReturn): void
    {
        /** @var MockObject|FileSystem */
        $filesystem = $this->getMockBuilder(FileSystem::class)->getMock();
        $emailLister = new EmailLister($filesystem);
        $this->assertSame($expectedReturn, $emailLister->getCleanedMailName($mailName));
    }

    public function providerGetCleanedMailName(): iterable
    {
        yield ['', ''];
        yield ['.html', ''];
        yield ['a.html', 'A'];
        yield ['cart', 'Cart'];
        yield ['MAIL', 'MAIL'];
        yield ['order_history', 'Order history'];
        yield ['my-cart', 'My cart'];
        yield ['-_-_-', ''];
    }
}
