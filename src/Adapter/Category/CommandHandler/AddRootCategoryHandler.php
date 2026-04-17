<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Image\Uploader\CategoryImageUploader;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\AddRootCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotAddCategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;

/**
 * Class AddRootCategoryHandler.
 */
#[AsCommandHandler]
final class AddRootCategoryHandler extends AbstractEditCategoryHandler implements AddRootCategoryHandlerInterface
{
    public function __construct(
        private readonly ConfigurationInterface $configuration,
        CategoryImageUploader $categoryImageUploader,
        CategoryRepository $categoryRepository,
    ) {
        parent::__construct($categoryImageUploader, $categoryRepository);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddRootCategoryCommand $command)
    {
        /** @var Category $category */
        $category = $this->createRootCategoryFromCommand($command);

        $categoryId = new CategoryId((int) $category->id);

        $this->categoryImageUploader->uploadImages(
            $categoryId,
            $command->getCoverImage(),
            $command->getThumbnailImage()
        );

        return $categoryId;
    }

    /**
     * Creates legacy root category
     *
     * @param AddRootCategoryCommand $command
     *
     * @return Category
     *
     * @throws CannotAddCategoryException
     * @throws CategoryException
     */
    private function createRootCategoryFromCommand(AddRootCategoryCommand $command)
    {
        $category = new Category();
        $category->is_root_category = true;
        $category->level_depth = 1;
        $category->id_parent = $this->configuration->get('PS_ROOT_CATEGORY');
        $category->name = $command->getLocalizedNames();
        $category->link_rewrite = $command->getLocalizedLinkRewrites();
        $category->active = $command->isActive();

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
            throw new CategoryException('Invalid data for creating root category.');
        }

        if (false === $category->validateFieldsLang(false)) {
            throw new CategoryException('Invalid language data for creating root category.');
        }

        if (null !== $command->getRedirectOption()) {
            $this->fillWithRedirectOption($category, $command->getRedirectOption());
        }

        if (false === $category->save()) {
            throw new CannotAddCategoryException('Failed to create root category.');
        }

        if ($command->getAssociatedShopIds()) {
            $this->associateWithShops($category, $command->getAssociatedShopIds());
        }

        return $category;
    }
}
