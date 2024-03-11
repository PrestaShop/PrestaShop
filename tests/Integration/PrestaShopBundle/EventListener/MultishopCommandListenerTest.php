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

namespace Tests\Integration\PrestaShopBundle\EventListener;

use LogicException;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShopBundle\EventListener\Console\MultishopCommandListener;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class MultishopCommandListenerTest extends KernelTestCase
{
    /**
     * @var MultishopCommandListener
     */
    public $commandListener;

    /**
     * @var Context
     */
    public $multishopContext;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->commandListener = self::$kernel->getContainer()->get('prestashop.multishop_command_listener');
        $this->multishopContext = self::$kernel->getContainer()->get('prestashop.adapter.shop.context');
    }

    public function testDefaultMultishopContext(): void
    {
        Shop::resetContext();
        $this->assertFalse($this->multishopContext->isShopContext(), 'isShopContext');
        $this->assertFalse($this->multishopContext->isGroupShopContext(), 'isGroupShopContext');
        $this->assertFalse($this->multishopContext->isAllShopContext(), 'isAllShopContext');
    }

    public function testSetShopID(): void
    {
        // Prepare ...
        $command = new Command('Fake');
        $input = new StringInput('--id_shop=1');
        $output = new NullOutput();
        $event = new ConsoleCommandEvent($command, $input, $output);

        // Call ...
        $this->commandListener->onConsoleCommand($event);

        // Check!
        $this->assertTrue($this->multishopContext->isShopContext(), 'isShopContext');
    }

    public function testSetShopGroupID(): void
    {
        // Prepare ...
        $command = new Command('Fake');
        $input = new StringInput('--id_shop_group=1');
        $output = new NullOutput();
        $event = new ConsoleCommandEvent($command, $input, $output);

        // Call ...
        $this->commandListener->onConsoleCommand($event);

        // Check!
        $this->assertTrue($this->multishopContext->isGroupShopContext());
    }

    public function testExceptionWhenIdShopAndIdShopGroupSet(): void
    {
        // Prepare ...
        $command = new Command('Fake');
        $input = new StringInput('--id_shop=2 --id_shop_group=1');
        $output = new NullOutput();
        $event = new ConsoleCommandEvent($command, $input, $output);

        // Call ...
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Do not specify an ID shop and an ID group shop at the same time.'
        );
        $this->commandListener->onConsoleCommand($event);
    }
}
