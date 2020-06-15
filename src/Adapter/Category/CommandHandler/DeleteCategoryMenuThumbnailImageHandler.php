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
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryMenuThumbnailImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\DeleteCategoryMenuThumbnailImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotDeleteImageException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Handles command which deletes given category menu thumbnail.
 *
 * @internal
 */
final class DeleteCategoryMenuThumbnailImageHandler implements DeleteCategoryMenuThumbnailImageHandlerInterface
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
     * @var CacheClearerInterface
     */
    private $smartyCacheClearer;

    /**
     * @param Filesystem $filesystem
     * @param ConfigurationInterface $configuration
     * @param CacheClearerInterface $smartyCacheClearer
     */
    public function __construct(
        Filesystem $filesystem,
        ConfigurationInterface $configuration,
        CacheClearerInterface $smartyCacheClearer
    ) {
        $this->filesystem = $filesystem;
        $this->configuration = $configuration;
        $this->smartyCacheClearer = $smartyCacheClearer;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteCategoryMenuThumbnailImageCommand $command)
    {
        $categoryId = $command->getCategoryId();
        $menuThumbnailId = $command->getMenuThumbnailId();

        $category = new Category($categoryId->getValue());

        if ($category->id !== $categoryId->getValue()) {
            throw new CategoryNotFoundException($categoryId, sprintf('Category with id "%s" was not found', $categoryId));
        }

        $thumbnailPath = sprintf(
            '%s%s-%s_thumb.jpg',
            $this->configuration->get('_PS_CAT_IMG_DIR_'),
            $category->id,
            $menuThumbnailId->getValue()
        );

        try {
            if ($this->filesystem->exists($thumbnailPath)) {
                $this->filesystem->remove($thumbnailPath);

                $this->smartyCacheClearer->clear();
            }
        } catch (IOException $e) {
            throw new CannotDeleteImageException(sprintf('Cannot delete menu thumbnail with id "%s" for category with id "%s".', $menuThumbnailId->getValue(), $categoryId->getValue()), CannotDeleteImageException::MENU_THUMBNAIL_IMAGE, $e);
        }
    }
}
