<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShopBundle\DependencyInjection\Compiler\CommandAndQueryRegisterPass;
use Symfony\Component\DependencyInjection\Compiler\AttributeAutoconfigurationPass;
use Symfony\Component\DependencyInjection\Compiler\ResolveInstanceofConditionalsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DummyCommand
{
}

class DummyQuery
{
}

#[AsCommandHandler(transactional: true)]
class DummyCommandHandler
{
    public function handle(DummyCommand $command): void
    {
    }
}

#[AsQueryHandler]
class DummyQueryHandler
{
    public function handle(DummyQuery $query): void
    {
    }
}

class CommandAndQueryRegisterPassTest extends TestCase
{
    public function testProcessRegistersMessengerTagsWithTransactionalAttribute(): void
    {
        $container = new ContainerBuilder();
        $pass = new CommandAndQueryRegisterPass();

        $container->register(DummyCommandHandler::class, DummyCommandHandler::class)
            ->setAutoconfigured(true);

        $container->register(DummyQueryHandler::class, DummyQueryHandler::class)
            ->setAutoconfigured(true);

        $pass->process($container);
        (new AttributeAutoconfigurationPass())->process($container);
        (new ResolveInstanceofConditionalsPass())->process($container);

        $commandHandlerDefinition = $container->getDefinition(DummyCommandHandler::class);
        $commandTags = $commandHandlerDefinition->getTag('messenger.message_handler');

        $this->assertNotEmpty($commandTags);
        $this->assertEquals([
            'method' => 'handle',
            'handles' => DummyCommand::class,
            'transactional' => true,
        ], $commandTags[0]);

        $queryHandlerDefinition = $container->getDefinition(DummyQueryHandler::class);
        $queryTags = $queryHandlerDefinition->getTag('messenger.message_handler');

        $this->assertNotEmpty($queryTags);
        $this->assertEquals([
            'method' => 'handle',
            'handles' => DummyQuery::class,
        ], $queryTags[0]);
    }
}
