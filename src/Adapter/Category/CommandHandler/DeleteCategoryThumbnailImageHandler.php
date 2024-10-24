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
use ImageType;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryThumbnailImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\DeleteCategoryThumbnailImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotDeleteImageException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Handles category thumbnail image deleting command.
 *
 * @internal
 */
#[AsCommandHandler]
final class DeleteCategoryThumbnailImageHandler implements DeleteCategoryThumbnailImageHandlerInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param Filesystem $filesystem
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        Filesystem $filesystem,
        ConfigurationInterface $configuration
    ) {
        $this->filesystem = $filesystem;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteCategoryThumbnailImageCommand $command)
    {
        $categoryId = $command->getCategoryId();
        $category = new Category($categoryId->getValue());

        $this->assertCategoryExists($categoryId, $category);

        $this->deleteThumbnailImage($category);
        $this->deleteTemporaryThumbnailImage($category);
        $this->deleteImagesForAllTypes($category);
    }

    /**
     * @param CategoryId $categoryId
     * @param Category $category
     *
     * @throws CategoryNotFoundException
     */
    private function assertCategoryExists(CategoryId $categoryId, Category $category)
    {
        if ($category->id !== $categoryId->getValue()) {
            throw new CategoryNotFoundException($categoryId, sprintf('Category with id "%s" was not found.', $categoryId->getValue()));
        }
    }

    /**
     * @param Category $category
     *
     * @throws CannotDeleteImageException
     */
    private function deleteThumbnailImage(Category $category)
    {
        $thumbnailPath = $this->configuration->get('_PS_CAT_IMG_DIR_') . $category->id . '_thumb.jpg';

        try {
            if ($this->filesystem->exists($thumbnailPath)) {
                $this->filesystem->remove($thumbnailPath);
            }
        } catch (IOException $e) {
            throw new CannotDeleteImageException(sprintf('Cannot delete thumbnail image for category with id "%s"', $category->id), CannotDeleteImageException::THUMBNAIL_IMAGE, $e);
        }
    }

    /**
     * @param Category $category
     *
     * @throws CannotDeleteImageException
     */
    private function deleteTemporaryThumbnailImage(Category $category)
    {
        $temporaryThumbnailPath = $this->configuration->get('_PS_TMP_IMG_DIR_') . 'category_' . $category->id . '-thumb.jpg';

        try {
            if ($this->filesystem->exists($temporaryThumbnailPath)) {
                $this->filesystem->remove($temporaryThumbnailPath);
            }
        } catch (IOException $e) {
            throw new CannotDeleteImageException(sprintf('Cannot delete thumbnail image for category with id "%s"', $category->id), CannotDeleteImageException::THUMBNAIL_IMAGE, $e);
        }
    }

    /**
     * @param Category $category
     *
     * @throws CannotDeleteImageException
     */
    private function deleteImagesForAllTypes(Category $category)
    {
        $imageTypes = ImageType::getImagesTypes('categories');
        $categoryImageDir = $this->configuration->get('_PS_CAT_IMG_DIR_');

        try {
            foreach ($imageTypes as $imageType) {
                foreach (ImageFormatConfiguration::SUPPORTED_FORMATS as $imageFormat) {
                    $imagePath = $categoryImageDir . $category->id . '_thumb-' . $imageType['name'] . '.' . $imageFormat;
                    if ($this->filesystem->exists($imagePath)) {
                        $this->filesystem->remove($imagePath);
                    }
                }
            }
        } catch (IOException $e) {
            throw new CannotDeleteImageException(
                sprintf(
                    'Cannot delete image with type "%s" for category with id "%s"',
                    isset($imageType) ? $imageType['name'] : '',
                    $category->id
                ),
                CannotDeleteImageException::COVER_IMAGE,
                $e
            );
        }
    }
}
