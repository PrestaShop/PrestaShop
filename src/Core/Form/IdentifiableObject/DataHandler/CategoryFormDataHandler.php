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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\MenuThumbnailsLimitException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\MenuThumbnailId;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Creates/updates category from data submitted in category form
 */
class CategoryFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    /**
     * @var ImageUploaderInterface
     */
    protected $categoryCoverUploader;

    /**
     * @var ImageUploaderInterface
     */
    protected $categoryThumbnailUploader;

    /**
     * @var ImageUploaderInterface
     */
    protected $categoryMenuThumbnailUploader;

    /**
     * @param CommandBusInterface $commandBus
     * @param ImageUploaderInterface $categoryCoverUploader
     * @param ImageUploaderInterface $categoryThumbnailUploader
     * @param ImageUploaderInterface $categoryMenuThumbnailUploader
     */
    public function __construct(
        CommandBusInterface $commandBus,
        ImageUploaderInterface $categoryCoverUploader,
        ImageUploaderInterface $categoryThumbnailUploader,
        ImageUploaderInterface $categoryMenuThumbnailUploader
    ) {
        $this->commandBus = $commandBus;
        $this->categoryCoverUploader = $categoryCoverUploader;
        $this->categoryThumbnailUploader = $categoryThumbnailUploader;
        $this->categoryMenuThumbnailUploader = $categoryMenuThumbnailUploader;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        if (!isset($data['menu_thumbnail_images']) || count($data['menu_thumbnail_images']) > count(MenuThumbnailId::ALLOWED_ID_VALUES)) {
            throw new MenuThumbnailsLimitException('Maximum number of menu thumbnails exceeded for new category');
        }
        $command = $this->createAddCategoryCommand($data);

        /** @var CategoryId $categoryId */
        $categoryId = $this->commandBus->handle($command);

        /**
         * In some cases in form menu_thumbnail_images can be disabled so value won't get here.
         */
        $menuThumbnailImages = $data['menu_thumbnail_images'] ?? [];
        $this->uploadImages(
            $categoryId,
            $data['cover_image'],
            $data['thumbnail_image'],
            $menuThumbnailImages
        );

        return $categoryId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($categoryId, array $data)
    {
        $availableKeys = $this->getAvailableKeys((int) $categoryId);

        if (!isset($data['menu_thumbnail_images']) || count($data['menu_thumbnail_images']) > count($availableKeys)) {
            throw new MenuThumbnailsLimitException(sprintf('Maximum number of menu thumbnails was reached for category "%d"', $categoryId));
        }
        $command = $this->createEditCategoryCommand($categoryId, $data);

        $this->commandBus->handle($command);
        $categoryId = new CategoryId((int) $categoryId);

        /**
         * In some cases in form menu_thumbnail_images can be disabled so value won't get here.
         */
        $menuThumbnailImages = $data['menu_thumbnail_images'] ?? [];

        $this->uploadImages(
            $categoryId,
            $data['cover_image'],
            $data['thumbnail_image'],
            $menuThumbnailImages
        );
    }

    /**
     * Creates add category command from form data
     *
     * @param array $data
     *
     * @return AddCategoryCommand
     */
    protected function createAddCategoryCommand(array $data)
    {
        $command = new AddCategoryCommand(
            $data['name'],
            $data['link_rewrite'],
            (bool) $data['active'],
            (int) $data['id_parent']
        );

        $command->setLocalizedDescriptions($data['description']);
        $command->setLocalizedAdditionalDescriptions($data['additional_description']);
        $command->setLocalizedMetaTitles($data['meta_title']);
        $command->setLocalizedMetaDescriptions($data['meta_description']);
        $command->setLocalizedMetaKeywords($data['meta_keyword']);
        $command->setAssociatedGroupIds($data['group_association']);

        if (isset($data['shop_association'])) {
            $command->setAssociatedShopIds($data['shop_association']);
        }

        return $command;
    }

    /**
     * Creates edit category command from
     *
     * @param int $categoryId
     * @param array $data
     *
     * @return EditCategoryCommand
     */
    protected function createEditCategoryCommand($categoryId, array $data)
    {
        $command = new EditCategoryCommand($categoryId);
        $command->setIsActive($data['active']);
        $command->setLocalizedLinkRewrites($data['link_rewrite']);
        $command->setLocalizedNames($data['name']);
        $command->setParentCategoryId($data['id_parent']);
        $command->setLocalizedDescriptions($data['description']);
        $command->setLocalizedAdditionalDescriptions($data['additional_description']);
        $command->setLocalizedMetaTitles($data['meta_title']);
        $command->setLocalizedMetaDescriptions($data['meta_description']);
        $command->setLocalizedMetaKeywords($data['meta_keyword']);
        $command->setAssociatedGroupIds($data['group_association']);

        if (isset($data['shop_association'])) {
            $command->setAssociatedShopIds($data['shop_association']);
        }

        return $command;
    }

    /**
     * @param CategoryId $categoryId
     * @param UploadedFile|null $coverImage
     * @param UploadedFile|null $thumbnailImage
     * @param UploadedFile[] $menuThumbnailImages
     */
    protected function uploadImages(
        CategoryId $categoryId,
        UploadedFile $coverImage = null,
        UploadedFile $thumbnailImage = null,
        array $menuThumbnailImages = []
    ) {
        if (null !== $coverImage) {
            $this->categoryCoverUploader->upload($categoryId->getValue(), $coverImage);
        }

        if (null !== $thumbnailImage) {
            $this->categoryThumbnailUploader->upload($categoryId->getValue(), $thumbnailImage);
        }

        if (!empty($menuThumbnailImages)) {
            foreach ($menuThumbnailImages as $menuThumbnail) {
                $this->categoryMenuThumbnailUploader->upload($categoryId->getValue(), $menuThumbnail);
            }
        }
    }

    /**
     * @param int $categoryId
     *
     * @return array
     */
    protected function getAvailableKeys(int $categoryId): array
    {
        $files = scandir(_PS_CAT_IMG_DIR_, SCANDIR_SORT_NONE);
        $usedKeys = [];

        foreach ($files as $file) {
            $matches = [];

            if (preg_match('/^' . $categoryId . '-([0-9])?_thumb.jpg/i', $file, $matches) === 1) {
                $usedKeys[] = (int) $matches[1];
            }
        }

        return array_diff(MenuThumbnailId::ALLOWED_ID_VALUES, $usedKeys);
    }
}
