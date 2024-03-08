<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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

        if (null !== $command->getLocalizedMetaKeywords()) {
            $category->meta_keywords = $command->getLocalizedMetaKeywords();
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

        if (false === $category->save()) {
            throw new CannotAddCategoryException('Failed to create root category.');
        }

        if (null !== $command->getRedirectOption()) {
            $this->fillWithRedirectOption($category, $command->getRedirectOption());
        }

        if ($command->getAssociatedShopIds()) {
            $this->associateWithShops($category, $command->getAssociatedShopIds());
        }

        return $category;
    }
}
