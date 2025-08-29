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

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Adapter\ImageThumbnailsRegenerator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\RegenerateThumbnailsCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\RegenerateThumbnailsTimeoutException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\RegenerateThumbnailsWriteException;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShopBundle\Entity\Repository\ImageTypeRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommandHandler]
final class RegenerateThumbnailsHandler extends AbstractObjectModelHandler implements RegenerateThumbnailsHandlerInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ImageTypeRepository $imageTypeRepository,
        private readonly LanguageRepositoryInterface $langRepository,
        private readonly ImageThumbnailsRegenerator $imageThumbnailsRegenerator,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws ImageTypeException
     */
    public function handle(RegenerateThumbnailsCommand $command): void
    {
        // Get all languages
        $languages = $this->langRepository->findAll();

        // Set images to regenerate with all theirs specific directories
        $process = [
            ['type' => 'categories', 'dir' => _PS_CAT_IMG_DIR_],
            ['type' => 'manufacturers', 'dir' => _PS_MANU_IMG_DIR_],
            ['type' => 'suppliers', 'dir' => _PS_SUPP_IMG_DIR_],
            ['type' => 'products', 'dir' => _PS_PRODUCT_IMG_DIR_],
            ['type' => 'stores', 'dir' => _PS_STORE_IMG_DIR_],
        ];

        // Launching generation process
        foreach ($process as $proc) {
            // Check if this kind of image is selected, if not, skip
            if ($command->getImage() !== 'all' && $command->getImage() !== $proc['type']) {
                continue;
            }

            // Getting formats generation (all if 'all' selected)
            if ($command->getImageTypeId() === 0) {
                $formats = $this->imageTypeRepository->findBy([$proc['type'] => 1]);
            } else {
                $formats = $this->imageTypeRepository->findBy(['id' => $command->getImageTypeId(), $proc['type'] => 1]);
            }

            // If user asked to erase images, let's do it first
            if ($command->erasePreviousImages()) {
                $this->imageThumbnailsRegenerator->deletePreviousImages($proc['dir'], $formats, $proc['type'] === 'products');
            }

            // Regenerate images
            $errors = $this->imageThumbnailsRegenerator->regenerateNewImages($proc['dir'], $formats, $proc['type'] == 'products');
            if (is_array($errors) && count($errors) > 0) {
                if (in_array('timeout', $errors)) {
                    throw new RegenerateThumbnailsTimeoutException($this->translator->trans('Only part of the images have been regenerated. The server timed out before finishing.', [], 'Admin.Design.Notification'));
                } else {
                    throw new RegenerateThumbnailsWriteException($this->translator->trans('Cannot write images for this type: %1$s. Please check the %2$s folder\'s writing permissions.', [$proc['type'], $proc['dir']], 'Admin.Design.Notification'));
                }
            } else {
                if ($proc['type'] == 'products') {
                    if ($this->imageThumbnailsRegenerator->regenerateWatermark($proc['dir'], $formats) === 'timeout') {
                        throw new RegenerateThumbnailsTimeoutException($this->translator->trans('Server timed out. The watermark may not have been applied to all images.', [], 'Admin.Design.Notification'));
                    }
                }
                if (count($errors) === 0) {
                    if ($this->imageThumbnailsRegenerator->regenerateNoPictureImages($proc['dir'], $formats, $languages)) {
                        throw new RegenerateThumbnailsTimeoutException($this->translator->trans('Cannot write images for this type: %1$s. Please check the %2$s folder\'s writing permissions.', [$proc['type'], $proc['dir']], 'Admin.Design.Notification'));
                    }
                }
            }
        }
    }
}
