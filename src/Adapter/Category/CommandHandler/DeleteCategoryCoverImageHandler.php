<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use ImageType;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryCoverImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\DeleteCategoryCoverImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotDeleteImageException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Handles category cover image deleting command.
 *
 * @internal
 */
#[AsCommandHandler]
final class DeleteCategoryCoverImageHandler implements DeleteCategoryCoverImageHandlerInterface
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
    public function handle(DeleteCategoryCoverImageCommand $command)
    {
        $categoryId = $command->getCategoryId();
        $category = new Category($categoryId->getValue());

        $this->assertCategoryExists($categoryId, $category);

        $this->deleteCoverImage($category);
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
    private function deleteCoverImage(Category $category)
    {
        if (false === $category->deleteImage(true)) {
            throw new CannotDeleteImageException(sprintf('Cannot delete cover image for category with id "%s"', $category->id), CannotDeleteImageException::COVER_IMAGE);
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
                    $imagePath = $categoryImageDir . $category->id . '-' . $imageType['name'] . '.' . $imageFormat;
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
