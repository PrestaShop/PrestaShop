<?php

namespace Tests\Integration\Adapter\Category\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotEditRootCategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EditRootCategoryHandlerTest extends KernelTestCase
{
    /**
     * @var object|\PrestaShop\PrestaShop\Core\CommandBus\TacticianCommandBusAdapter|null
     */
    private $commandBus;
    /**
     * @var int
     */
    private $rootCategory;
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|null
     */
    private $container;

    protected function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();

        $this->commandBus = $this->container->get('prestashop.core.command_bus');
        $this->rootCategory = (int) $this->container->get('prestashop.adapter.legacy.configuration')->get('PS_ROOT_CATEGORY');
    }

    public function testEditRootCategoryDoesntThrowExceptionIfExists()
    {
        $categories = $this->container->get('prestashop.adapter.form.choice_provider.category_tree_choice_provider')->getChoices();
        $existingCategoryId = $categories[0]['id_category'];
        $command = new EditRootCategoryCommand((int) $existingCategoryId);

        $this->assertNull($this->commandBus->handle($command));
    }

    public function testEditRootCategoryThrowsAnExceptionIfCategoryDoesntExist()
    {
        $command = new EditRootCategoryCommand(10000);
        $this->expectException(CategoryNotFoundException::class);
        $this->commandBus->handle($command);
    }

    public function testEditRootCategoryThrowsAnException()
    {
        $command = new EditRootCategoryCommand($this->rootCategory);
        $this->expectException(CannotEditRootCategoryException::class);
        $this->commandBus->handle($command);
    }
}
