<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\EditRootCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotEditCategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotEditRootCategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShopDatabaseException;
use PrestaShopException;

/**
 * Class EditRootCategoryHandler.
 */
#[AsCommandHandler]
final class EditRootCategoryHandler extends AbstractEditCategoryHandler implements EditRootCategoryHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param EditRootCategoryCommand $command
     *
     * @throws CannotEditCategoryException
     * @throws CannotEditRootCategoryException
     * @throws CategoryException
     * @throws CategoryNotFoundException
     */
    public function handle(EditRootCategoryCommand $command)
    {
        $category = new Category($command->getCategoryId()->getValue());

        if (!$category->id) {
            throw new CategoryNotFoundException($command->getCategoryId(), sprintf('Category with id "%s" cannot be found.', $command->getCategoryId()->getValue()));
        }

        if ($category->isRootCategory()) {
            throw new CannotEditRootCategoryException();
        }

        $this->updateRootCategoryFromCommandData($category, $command);

        $this->categoryImageUploader->uploadImages(
            $command->getCategoryId(),
            $command->getCoverImage(),
            $command->getThumbnailImage()
        );
    }

    /**
     * @param Category $category
     * @param EditRootCategoryCommand $command
     *
     * @throws CannotEditCategoryException
     * @throws CategoryException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function updateRootCategoryFromCommandData(Category $category, EditRootCategoryCommand $command)
    {
        if (null !== $command->isActive()) {
            $category->active = $command->isActive();
        }

        if (null !== $command->getLocalizedNames()) {
            $category->name = $command->getLocalizedNames();
        }

        if (null !== $command->getLocalizedLinkRewrites()) {
            $category->link_rewrite = $command->getLocalizedLinkRewrites();
        }

        if (null !== $command->getLocalizedDescriptions()) {
            $category->description = $command->getLocalizedDescriptions();
        }

        if (null !== $command->getLocalizedAdditionalDescriptions()) {
            $category->additional_description = $command->getLocalizedAdditionalDescriptions();
        }

        if (null !== $command->getLocalizedMetaTitles()) {
            $category->meta_title = $command->getLocalizedMetaTitles();
        }

        if (null !== $command->getLocalizedMetaDescriptions()) {
            $category->meta_description = $command->getLocalizedMetaDescriptions();
        }

        if (null !== $command->getAssociatedGroupIds()) {
            $category->groupBox = $command->getAssociatedGroupIds();
        }

        if (null !== $command->getRedirectOption()) {
            $this->fillWithRedirectOption($category, $command->getRedirectOption());
        }

        if ($command->getAssociatedShopIds()) {
            $this->associateWithShops($category, $command->getAssociatedShopIds());
        }

        if (false === $category->validateFields(false)) {
            throw new CategoryException('Invalid data for updating root category.');
        }

        if (false === $category->validateFieldsLang(false)) {
            throw new CategoryException('Invalid language data for updating root category.');
        }

        if (false === $category->update()) {
            throw new CannotEditCategoryException(sprintf('Failed to edit Category with id "%s".', $category->id));
        }
    }
}
