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

use PrestaShop\PrestaShop\Core\Category\Provider\MenuThumbnailAvailableKeyProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\MenuThumbnailsLimitException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\MenuThumbnailId;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Creates/updates root category from data submitted in category form
 *
 * @internal
 */
final class RootCategoryFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var ImageUploaderInterface
     */
    private $categoryCoverUploader;

    /**
     * @var ImageUploaderInterface
     */
    private $categoryThumbnailUploader;

    /**
     * @var ImageUploaderInterface
     */
    private $categoryMenuThumbnailUploader;

    /**
     * @var MenuThumbnailAvailableKeyProvider
     */
    private $menuThumbnailAvailableKeyProvider;

    /**
     * @param CommandBusInterface $commandBus
     * @param ImageUploaderInterface $categoryCoverUploader
     * @param ImageUploaderInterface $categoryThumbnailUploader
     * @param ImageUploaderInterface $categoryMenuThumbnailUploader
     * @param MenuThumbnailAvailableKeyProvider $menuThumbnailAvailableKeyProvider
     */
    public function __construct(
        CommandBusInterface $commandBus,
        ImageUploaderInterface $categoryCoverUploader,
        ImageUploaderInterface $categoryThumbnailUploader,
        ImageUploaderInterface $categoryMenuThumbnailUploader,
        MenuThumbnailAvailableKeyProvider $menuThumbnailAvailableKeyProvider
    ) {
        $this->commandBus = $commandBus;
        $this->categoryCoverUploader = $categoryCoverUploader;
        $this->categoryThumbnailUploader = $categoryThumbnailUploader;
        $this->categoryMenuThumbnailUploader = $categoryMenuThumbnailUploader;
        $this->menuThumbnailAvailableKeyProvider = $menuThumbnailAvailableKeyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        if (!isset($data['menu_thumbnail_images']) && count($data['menu_thumbnail_images']) > count(MenuThumbnailId::ALLOWED_ID_VALUES)) {
            throw new MenuThumbnailsLimitException('Maximum number of menu thumbnails exceeded for new category');
        }
        $command = $this->createAddRootCategoryCommand($data);

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
        $categoryId = (int) $categoryId;
        $availableKeys = $this->menuThumbnailAvailableKeyProvider->getAvailableKeys($categoryId);

        if (isset($data['menu_thumbnail_images']) && count($data['menu_thumbnail_images']) > count($availableKeys)) {
            throw new MenuThumbnailsLimitException(sprintf('The maximum number of menu thumbnails has been reached for the %d category', $categoryId));
        }
        $command = $this->createEditRootCategoryCommand($categoryId, $data);

        $this->commandBus->handle($command);
        $categoryId = new CategoryId($categoryId);

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
     * Creates command with form data for adding new root category
     *
     * @param array $data
     *
     * @return AddRootCategoryCommand
     *
     * @throws CategoryConstraintException
     */
    public function createAddRootCategoryCommand(array $data): AddRootCategoryCommand
    {
        $command = new AddRootCategoryCommand(
            $data['name'],
            $data['link_rewrite'],
            $data['active']
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
     * @param int $rootCategoryId
     * @param array $data
     *
     * @return EditRootCategoryCommand
     */
    private function createEditRootCategoryCommand(int $rootCategoryId, array $data): EditRootCategoryCommand
    {
        $command = new EditRootCategoryCommand($rootCategoryId);
        $command->setIsActive($data['active']);
        $command->setLocalizedLinkRewrites($data['link_rewrite']);
        $command->setLocalizedNames($data['name']);
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
    private function uploadImages(
        CategoryId $categoryId,
        UploadedFile $coverImage = null,
        UploadedFile $thumbnailImage = null,
        array $menuThumbnailImages = []
    ): void {
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
}
