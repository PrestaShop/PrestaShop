<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Adapter\Category\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotEditRootCategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class EditRootCategoryHandlerTest extends KernelTestCase
{
    /**
     * @var object|CommandBusInterface|null
     */
    private $commandBus;
    /**
     * @var int
     */
    private $rootCategory;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::resetDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::resetDatabase();
    }

    protected static function resetDatabase(): void
    {
        DatabaseDump::restoreTables([
            'category',
            'category_lang',
            'category_group',
            'category_shop',
        ]);
    }

    protected function setUp(): void
    {
        self::bootKernel();

        $this->commandBus = self::getContainer()->get('prestashop.core.command_bus');
        $this->rootCategory = (int) self::getContainer()->get('prestashop.adapter.legacy.configuration')->get('PS_ROOT_CATEGORY');
    }

    public function testEditRootCategoryDoesntThrowExceptionIfExists(): void
    {
        $categories = self::getContainer()->get('prestashop.adapter.form.choice_provider.category_tree_choice_provider')->getChoices();
        $existingCategoryId = $categories[0]['id_category'];
        $command = new EditRootCategoryCommand((int) $existingCategoryId);

        $this->assertNull($this->commandBus->handle($command));
    }

    public function testEditRootCategoryThrowsAnExceptionIfCategoryDoesntExist(): void
    {
        $command = new EditRootCategoryCommand(10000);
        $this->expectException(CategoryNotFoundException::class);
        $this->commandBus->handle($command);
    }

    public function testEditRootCategoryThrowsAnException(): void
    {
        $command = new EditRootCategoryCommand($this->rootCategory);
        $this->expectException(CannotEditRootCategoryException::class);
        $this->commandBus->handle($command);
    }
}
