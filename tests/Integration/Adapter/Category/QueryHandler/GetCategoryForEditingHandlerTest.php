<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Adapter\Category\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotEditRootCategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class GetCategoryForEditingHandlerTest extends KernelTestCase
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

    public function testGetCategoryForEditingReturnsAnEditableCategoryIfExists(): void
    {
        $categories = self::getContainer()->get('prestashop.adapter.form.choice_provider.category_tree_choice_provider')->getChoices();
        $existingCategoryId = $categories[0]['id_category'];
        $command = new GetCategoryForEditing((int) $existingCategoryId);

        $editableCategory = $this->commandBus->handle($command);
        $this->assertInstanceOf(EditableCategory::class, $editableCategory);
    }

    public function testGetRootCategoryForEditingThrowsAnExceptionIfCategoryDoesntExist(): void
    {
        $command = new GetCategoryForEditing(10000);
        $this->expectException(CategoryNotFoundException::class);
        $this->commandBus->handle($command);
    }

    public function testGetRootCategoryForEditingThrowsAnException(): void
    {
        $command = new GetCategoryForEditing($this->rootCategory);
        $this->expectException(CannotEditRootCategoryException::class);
        $this->commandBus->handle($command);
    }
}
