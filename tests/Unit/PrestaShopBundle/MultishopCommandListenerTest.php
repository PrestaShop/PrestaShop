<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\PrestaShopBundle;

use Tests\TestCase\UnitTestCase;
use Tests\Unit\ContextMocker;
use PrestaShopBundle\EventListener\MultishopCommandListener;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use Shop;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class MultishopCommandListenerTest extends UnitTestCase
{
    /**
     * @var MultishopCommandListener
     */
    public $commandListener;

    /**
     *
     * @var Context
     */
    public $multishopContext;

    /**
     * @var ContextMocker
     */
    protected $contextMocker;

    public function setUp()
    {

        $this->contextMocker = new ContextMocker();
        $this->contextMocker->mockContext();

        parent::setUp();

        $this->setupSfKernel();

        $this->commandListener = $this->sfKernel->getContainer()->get('prestashop.multishop_command_listener');
        $this->multishopContext = $this->sfKernel->getContainer()->get('prestashop.adapter.shop.context');
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->contextMocker->resetContext();
    }


    public function testDefaultMultishopContext()
    {
        Shop::resetContext();
        $this->assertFalse($this->multishopContext->isShopContext(), 'isShopContext');
        $this->assertFalse($this->multishopContext->isShopGroupContext(), 'isShopGroupContext');
        $this->assertFalse($this->multishopContext->isAllContext(), 'isAllContext');
    }

    public function testSetShopID()
    {
        // Prepare ...
        $command = new Command('Fake');
        $input   = new StringInput('--id_shop=1');
        $output  = new NullOutput();
        $event   = new ConsoleCommandEvent($command, $input, $output);

        // Call ...
        $this->commandListener->onConsoleCommand($event);

        // Check!
        $this->assertTrue($this->multishopContext->isShopContext(), 'isShopContext');
    }

    public function testSetShopGroupID()
    {
        // Prepare ...
        $command = new Command('Fake');
        $input   = new StringInput('--id_shop_group=1');
        $output  = new NullOutput();
        $event   = new ConsoleCommandEvent($command, $input, $output);

        // Call ...
        $this->commandListener->onConsoleCommand($event);

        // Check!
        $this->assertTrue($this->multishopContext->isShopGroupContext());
    }

    public function testExceptionWhenIdShopAndIdShopGroupSet()
    {
        // Prepare ...
        $command = new Command('Fake');
        $input   = new StringInput('--id_shop=2 --id_shop_group=1');
        $output  = new NullOutput();
        $event   = new ConsoleCommandEvent($command, $input, $output);

        // Call ...
        $this->setExpectedException(
            'LogicException',
            'Do not specify an ID shop and an ID group shop at the same time.'
        );
        $this->commandListener->onConsoleCommand($event);
    }
}
