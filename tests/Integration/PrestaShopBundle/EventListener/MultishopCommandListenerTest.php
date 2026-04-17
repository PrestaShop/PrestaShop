<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

        $this->multishopContext = self::$kernel->getContainer()->get('prestashop.adapter.shop.context');
        $this->commandListener = new MultishopCommandListener($this->multishopContext, self::$kernel->getProjectDir());
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
