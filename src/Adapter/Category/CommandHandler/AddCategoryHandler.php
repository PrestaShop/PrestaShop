<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\AddCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotAddCategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShopDatabaseException;
use PrestaShopException;

/**
 * Adds new category using legacy object model.
 *
 * @internal
 */
#[AsCommandHandler]
final class AddCategoryHandler extends AbstractEditCategoryHandler implements AddCategoryHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param AddCategoryCommand $command
     *
     * @return CategoryId
     */
    public function handle(AddCategoryCommand $command)
    {
        $category = $this->createCategoryFromCommand($command);

        $categoryId = new CategoryId((int) $category->id);

        $this->categoryImageUploader->uploadImages(
            $categoryId,
            $command->getCoverImage(),
            $command->getThumbnailImage()
        );

        return $categoryId;
    }

    /**
     * @param AddCategoryCommand $command
     *
     * @return Category
     *
     * @throws CannotAddCategoryException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function createCategoryFromCommand(AddCategoryCommand $command)
    {
        $category = new Category();
        $category->id_parent = $command->getParentCategoryId();
        $category->active = $command->isActive();

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

        if (false === $category->validateFields(false)) {
            throw new CannotAddCategoryException('Invalid data for creating category.');
        }

        if (false === $category->validateFieldsLang(false)) {
            throw new CannotAddCategoryException('Invalid language data for creating category.');
        }

        if (null !== $command->getRedirectOption()) {
            $this->fillWithRedirectOption($category, $command->getRedirectOption());
        }

        if (false === $category->add()) {
            throw new CannotAddCategoryException('Failed to add new category.');
        }

        if ($command->getAssociatedShopIds()) {
            $this->associateWithShops($category, $command->getAssociatedShopIds());
        }

        return $category;
    }
}
