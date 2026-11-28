<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\RedirectType;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\CategoryFormDataHandler;

class CategoryFormDataHandlerTest extends TestCase
{
    public function testUpdatePreservesGroupsOutsideCurrentShopContext(): void
    {
        $categoryId = 42;

        // Groups 1, 2 and 3 belong to the current shop context.
        $contextGroupIds = [1, 2, 3];

        // Groups 8 and 9 are already associated with the category,
        // but belong to another shop and are therefore hidden from this form.
        $currentCategoryGroupIds = [1, 2, 3, 8, 9];

        // The user deliberately deselects group 2 in the current shop.
        $submittedGroupIds = [1, 3];

        $editableCategory = $this->createMock(EditableCategory::class);
        $editableCategory
            ->expects($this->once())
            ->method('getGroupAssociationIds')
            ->willReturn($currentCategoryGroupIds);

        $groupDataProvider = $this->createMock(GroupDataProvider::class);
        $groupDataProvider
            ->expects($this->once())
            ->method('getAllGroupIds')
            ->with(true)
            ->willReturn($contextGroupIds);

        $editCommand = null;

        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->willReturnCallback(
                function ($command) use (&$editCommand, $editableCategory) {
                    if ($command instanceof GetCategoryForEditing) {
                        return $editableCategory;
                    }

                    if ($command instanceof EditCategoryCommand) {
                        $editCommand = $command;
                    }

                    return null;
                }
            );

        $dataHandler = new CategoryFormDataHandler(
            $commandBus,
            $groupDataProvider
        );

        $dataHandler->update(
            $categoryId,
            $this->createCategoryFormData($submittedGroupIds)
        );

        $this->assertInstanceOf(EditCategoryCommand::class, $editCommand);

        // Group 2 was visible in the current context and intentionally unchecked,
        // so it must be removed. Groups 8 and 9 were outside the current context
        // and must be preserved.
        $this->assertSame(
            [1, 3, 8, 9],
            $editCommand->getAssociatedGroupIds()
        );
    }

    public function testUpdateDoesNotRestoreDeselectedGroupsFromCurrentContext(): void
    {
        $categoryId = 42;

        $editableCategory = $this->createMock(EditableCategory::class);
        $editableCategory
            ->expects($this->once())
            ->method('getGroupAssociationIds')
            ->willReturn([1, 2, 3]);

        $groupDataProvider = $this->createMock(GroupDataProvider::class);
        $groupDataProvider
            ->expects($this->once())
            ->method('getAllGroupIds')
            ->with(true)
            ->willReturn([1, 2, 3]);

        $editCommand = null;

        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->willReturnCallback(
                function ($command) use (&$editCommand, $editableCategory) {
                    if ($command instanceof GetCategoryForEditing) {
                        return $editableCategory;
                    }

                    if ($command instanceof EditCategoryCommand) {
                        $editCommand = $command;
                    }

                    return null;
                }
            );

        $dataHandler = new CategoryFormDataHandler(
            $commandBus,
            $groupDataProvider
        );

        $dataHandler->update(
            $categoryId,
            $this->createCategoryFormData([1, 3])
        );

        $this->assertInstanceOf(EditCategoryCommand::class, $editCommand);

        $this->assertSame(
            [1, 3],
            $editCommand->getAssociatedGroupIds()
        );
    }

    /**
     * @param int[] $groupIds
     */
    private function createCategoryFormData(array $groupIds): array
    {
        return [
            'active' => true,
            'link_rewrite' => [1 => 'test-category'],
            'name' => [1 => 'Test category'],
            'id_parent' => 2,
            'description' => [1 => 'Description'],
            'additional_description' => [1 => 'Additional description'],
            'meta_title' => [1 => 'Meta title'],
            'meta_description' => [1 => 'Meta description'],
            'group_association' => $groupIds,
            'cover_image' => null,
            'thumbnail_image' => null,
            'redirect_option' => [
                'type' => RedirectType::TYPE_NOT_FOUND,
                'target' => null,
            ],
        ];
    }
}
