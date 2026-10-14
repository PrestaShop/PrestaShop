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
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;

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

    protected function tearDown(): void
    {
        Shop::resetContext();

        parent::tearDown();
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

    /**
     * The cases above build a Command with no Application attached, where getDefinition() falls back
     * to the command's own definition and nothing rebuilds it. A real console run has an Application,
     * and Command::run() then rebuilds its merged definition from the command's own one before
     * binding - so an option added to the merged copy is dropped and rejected as unknown. This runs
     * the command the way the console does.
     */
    public function testTheOptionsSurviveARealConsoleRun(): void
    {
        Shop::resetContext();

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener('console.command', [$this->commandListener, 'onConsoleCommand']);

        $command = new Command('fake');
        $command->setCode(static fn (): int => 0);

        $application = new Application();
        $application->setDispatcher($dispatcher);
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);
        $application->add($command);

        $exitCode = $application->run(new ArgvInput(['bin/console', 'fake', '--id_shop=1']), new NullOutput());

        $this->assertSame(0, $exitCode);
        $this->assertTrue($this->multishopContext->isShopContext(), 'isShopContext');
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
