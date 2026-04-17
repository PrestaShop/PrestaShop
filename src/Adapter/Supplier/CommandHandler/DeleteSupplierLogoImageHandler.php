<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use ImageType;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\DeleteSupplierLogoImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler\DeleteSupplierLogoImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Handles command which deletes supplier cover image using legacy object model
 */
#[AsCommandHandler]
class DeleteSupplierLogoImageHandler implements DeleteSupplierLogoImageHandlerInterface
{
    /**
     * @var string
     */
    protected $imageDir;

    /**
     * @var string
     */
    protected $tmpImageDir;

    public function __construct(string $imageDir, string $tmpImageDir)
    {
        $this->imageDir = $imageDir;
        $this->tmpImageDir = $tmpImageDir;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteSupplierLogoImageCommand $command): void
    {
        $fs = new Filesystem();

        $imageTypes = ImageType::getImagesTypes('suppliers');

        foreach ($imageTypes as $imageType) {
            foreach (ImageFormatConfiguration::SUPPORTED_FORMATS as $imageFormat) {
                $path = sprintf(
                    '%s%s-%s.' . $imageFormat,
                    $this->imageDir,
                    $command->getSupplierId()->getValue(),
                    stripslashes($imageType['name'])
                );
                if ($fs->exists($path)) {
                    $fs->remove($path);
                }
            }
        }

        $imagePath = sprintf(
            '%s%s.jpg',
            $this->imageDir,
            $command->getSupplierId()->getValue()
        );
        if ($fs->exists($imagePath)) {
            $fs->remove($imagePath);
        }

        // Delete tmp image
        $imgTmpPath = sprintf(
            '%ssupplier_%s.jpg',
            $this->tmpImageDir,
            $command->getSupplierId()->getValue()
        );
        if ($fs->exists($imgTmpPath)) {
            $fs->remove($imgTmpPath);
        }

        // Delete tmp image mini
        $imgMiniTmpPath = sprintf(
            '%ssupplier_mini_%s.jpg',
            $this->tmpImageDir,
            $command->getSupplierId()->getValue()
        );
        if ($fs->exists($imgMiniTmpPath)) {
            $fs->remove($imgMiniTmpPath);
        }
    }
}
