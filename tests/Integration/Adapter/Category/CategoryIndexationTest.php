<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Category;

use Category;
use Configuration;
use Context;
use Language;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The indexation switch is only useful if it survives the whole round trip the BO form makes:
 * command in, entity, query result back out.
 */
class CategoryIndexationTest extends KernelTestCase
{
    private $commandBus;
    private $queryBus;
    private ?int $createdCategoryId = null;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = self::getContainer();
        Context::getContext()->container = $container;
        $this->commandBus = $container->get('prestashop.core.command_bus');
        $this->queryBus = $container->get('prestashop.core.query_bus');
    }

    protected function tearDown(): void
    {
        if (null !== $this->createdCategoryId) {
            (new Category($this->createdCategoryId))->delete();
            $this->createdCategoryId = null;
        }
        parent::tearDown();
    }

    public function testACategoryCreatedWithoutIndexationIsStoredAsNotIndexable(): void
    {
        $this->createdCategoryId = $this->createCategory(false);

        $category = new Category($this->createdCategoryId);
        $this->assertFalse((bool) $category->indexation);
    }

    public function testIndexationDefaultsToIndexableWhenTheCommandDoesNotSetIt(): void
    {
        $this->createdCategoryId = $this->createCategory(null);

        $category = new Category($this->createdCategoryId);
        $this->assertTrue((bool) $category->indexation, 'Omitting the flag must leave the category indexable.');
    }

    public function testEditingTogglesIndexationAndTheQueryReturnsIt(): void
    {
        $this->createdCategoryId = $this->createCategory(false);

        $command = new EditCategoryCommand($this->createdCategoryId);
        $command->setIndexation(true);
        $this->commandBus->handle($command);

        $this->assertTrue((bool) (new Category($this->createdCategoryId))->indexation);

        /** @var EditableCategory $editable */
        $editable = $this->queryBus->handle(new GetCategoryForEditing($this->createdCategoryId));
        $this->assertTrue($editable->getIndexation(), 'The BO form must read back the stored value.');

        $command = new EditCategoryCommand($this->createdCategoryId);
        $command->setIndexation(false);
        $this->commandBus->handle($command);

        $editable = $this->queryBus->handle(new GetCategoryForEditing($this->createdCategoryId));
        $this->assertFalse($editable->getIndexation());
    }

    private function createCategory(?bool $indexation): int
    {
        $names = [];
        foreach (Language::getLanguages(false) as $language) {
            $names[(int) $language['id_lang']] = 'Indexation cmd 14317';
        }
        $rewrites = [];
        foreach (Language::getLanguages(false) as $language) {
            $rewrites[(int) $language['id_lang']] = 'indexation-cmd-14317';
        }

        $command = new AddCategoryCommand($names, $rewrites, true, (int) Configuration::get('PS_HOME_CATEGORY'));
        if (null !== $indexation) {
            $command->setIndexation($indexation);
        }

        return $this->commandBus->handle($command)->getValue();
    }
}
