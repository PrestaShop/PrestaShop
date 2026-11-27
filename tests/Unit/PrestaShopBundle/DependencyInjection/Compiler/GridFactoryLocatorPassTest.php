<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryProvider;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShopBundle\DependencyInjection\Compiler\GridFactoryLocatorPass;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class GridFactoryLocatorPassTest extends TestCase
{
    public function testItIndexesFactoriesByGridId(): void
    {
        $container = new ContainerBuilder();

        $provider = new Definition(GridFactoryProvider::class, [null]);
        $provider->setPublic(true);
        $container->setDefinition(GridFactoryProvider::class, $provider);

        $definitionFactory = new Definition(FakeGridDefinitionFactory::class);
        $container->setDefinition('fake.definition_factory', $definitionFactory);

        $conventionFactory = new Definition(FakeGridFactory::class, [new Reference('fake.definition_factory')]);
        $conventionFactory->addTag(GridFactoryLocatorPass::TAG_NAME);
        $container->setDefinition('fake.grid_factory.convention', $conventionFactory);

        $explicitFactory = new Definition(FakeGridFactory::class);
        $explicitFactory->addTag(GridFactoryLocatorPass::TAG_NAME, ['grid_id' => 'explicit_grid']);
        $container->setDefinition('fake.grid_factory.explicit', $explicitFactory);

        $unresolvableFactory = new Definition(FakeGridFactory::class);
        $unresolvableFactory->addTag(GridFactoryLocatorPass::TAG_NAME);
        $container->setDefinition('fake.grid_factory.unresolvable', $unresolvableFactory);

        $namedDefinitionFactory = new Definition(FakeNamedGridDefinitionFactory::class);
        $container->setDefinition('fake.definition_factory.named', $namedDefinitionFactory);
        $namedArgumentFactory = new Definition(FakeGridFactory::class);
        $namedArgumentFactory->setArgument('$definitionFactory', new Reference('fake.definition_factory.named'));
        $namedArgumentFactory->addTag(GridFactoryLocatorPass::TAG_NAME);
        $container->setDefinition('fake.grid_factory.named_argument', $namedArgumentFactory);

        (new GridFactoryLocatorPass())->process($container);
        $container->compile();

        /** @var GridFactoryProvider $gridFactoryProvider */
        $gridFactoryProvider = $container->get(GridFactoryProvider::class);

        $this->assertInstanceOf(FakeGridFactory::class, $gridFactoryProvider->getFactory('fake_grid'));
        $this->assertInstanceOf(FakeGridFactory::class, $gridFactoryProvider->getFactory('explicit_grid'));
        $this->assertInstanceOf(FakeGridFactory::class, $gridFactoryProvider->getFactory('named_fake_grid'));
        $this->assertNull($gridFactoryProvider->getFactory('unknown_grid'));
    }
}

class FakeGridDefinitionFactory implements GridDefinitionFactoryInterface
{
    public const GRID_ID = 'fake_grid';

    public function getDefinition(): GridDefinitionInterface
    {
        throw new RuntimeException('Not needed in this test');
    }
}

class FakeNamedGridDefinitionFactory implements GridDefinitionFactoryInterface
{
    public const GRID_ID = 'named_fake_grid';

    public function getDefinition(): GridDefinitionInterface
    {
        throw new RuntimeException('Not needed in this test');
    }
}

class FakeGridFactory implements GridFactoryInterface
{
    public function __construct(
        private readonly ?GridDefinitionFactoryInterface $definitionFactory = null,
    ) {
    }

    public function getGrid(SearchCriteriaInterface $searchCriteria): GridInterface
    {
        throw new RuntimeException('Not needed in this test: ' . get_debug_type($this->definitionFactory));
    }
}
