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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\CommandHandler;

use ImageType;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\DeleteManufacturerLogoImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler\DeleteManufacturerLogoImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Handles command which deletes manufacturer cover image using legacy object model
 */
class DeleteManufacturerLogoImageHandler extends AbstractManufacturerCommandHandler implements DeleteManufacturerLogoImageHandlerInterface
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
    public function handle(DeleteManufacturerLogoImageCommand $command): void
    {
        $fs = new Filesystem();

        $imageTypes = ImageType::getImagesTypes('manufacturers');

        // Get image formats we will be deleting. It would probably be easier to use ImageFormatConfiguration::SUPPORTED_FORMATS,
        // but we want to avoid any behavior change in minor/patch version.
        $configuredImageFormats = ServiceLocator::get(ImageFormatConfiguration::class)->getGenerationFormats();

        foreach ($imageTypes as $imageType) {
            foreach ($configuredImageFormats as $imageFormat) {
                $path = sprintf(
                    '%s%s-%s.' . $imageFormat,
                    $this->imageDir,
                    $command->getManufacturerId()->getValue(),
                    stripslashes($imageType['name'])
                );
                if ($fs->exists($path)) {
                    $fs->remove($path);
                }

                $path = sprintf(
                    '%s%s-%s2x.' . $imageFormat,
                    $this->imageDir,
                    $command->getManufacturerId()->getValue(),
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
            $command->getManufacturerId()->getValue()
        );
        if ($fs->exists($imagePath)) {
            $fs->remove($imagePath);
        }

        // Delete tmp image
        $imgTmpPath = sprintf(
            '%smanufacturer_%s.jpg',
            $this->tmpImageDir,
            $command->getManufacturerId()->getValue()
        );
        if ($fs->exists($imgTmpPath)) {
            $fs->remove($imgTmpPath);
        }

        // Delete tmp image mini
        $imgMiniTmpPath = sprintf(
            '%smanufacturer_mini_%s.jpg',
            $this->tmpImageDir,
            $command->getManufacturerId()->getValue()
        );
        if ($fs->exists($imgMiniTmpPath)) {
            $fs->remove($imgMiniTmpPath);
        }
    }
}
