<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AbstractCategoryCommand;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;

/**
 * Class AbstractAddCategoryHandler.
 *
 * @internal
 */
abstract class AbstractCategoryHandler
{
    /**
     * @var ImageUploaderInterface
     */
    private $categoryImageUploader;

    /**
     * @var ImageUploaderInterface
     */
    private $categoryThumbnailUploader;

    /**
     * @var ImageUploaderInterface
     */
    private $categoryMenuThumbnailUploader;

    /**
     * @param ImageUploaderInterface $categoryCoverUploader
     * @param ImageUploaderInterface $categoryThumbnailUploader
     * @param ImageUploaderInterface $categoryMenuThumbnailUploader
     */
    public function __construct(
        ImageUploaderInterface $categoryCoverUploader,
        ImageUploaderInterface $categoryThumbnailUploader,
        ImageUploaderInterface $categoryMenuThumbnailUploader
    ) {
        $this->categoryImageUploader = $categoryCoverUploader;
        $this->categoryThumbnailUploader = $categoryThumbnailUploader;
        $this->categoryMenuThumbnailUploader = $categoryMenuThumbnailUploader;
    }

    /**
     * Populate Category's object model with data from command so it can be used to create simple or root category.
     *
     * @param Category $category
     * @param AbstractCategoryCommand $command
     *
     * @return Category
     */
    protected function populateCategoryWithCommandData(Category $category, AbstractCategoryCommand $command)
    {
        if (null !== $command->getLocalizedNames()) {
            $category->name = $command->getLocalizedNames();
        }

        if (null !== $command->getLocalizedLinkRewrites()) {
            $category->link_rewrite = $command->getLocalizedLinkRewrites();
        }

        if (null !== $command->getLocalizedDescriptions()) {
            $category->description = $command->getLocalizedDescriptions();
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

        // This is a workaround to make Category's object model work.
        // Inside Category::add() & Category::update() method it checks if shop association is submitted
        // by retrieving data directly from $_POST["checkBoxShopAsso_category"].
        $_POST['checkBoxShopAsso_category'] = $command->getAssociatedShopIds();

        return $category;
    }

    /**
     * @param Category $category
     * @param AbstractCategoryCommand $command
     */
    protected function uploadImages(Category $category, AbstractCategoryCommand $command)
    {
        if (null !== $command->getCoverImage()) {
            $this->categoryImageUploader->upload(
                $category->id,
                $command->getCoverImage()
            );
        }

        if (null !== $command->getThumbnailImage()) {
            $this->categoryThumbnailUploader->upload(
                $category->id,
                $command->getThumbnailImage()
            );
        }

        if (!empty($menuThumbnails = $command->getMenuThumbnailImages())) {
            foreach ($menuThumbnails as $menuThumbnail) {
                $this->categoryMenuThumbnailUploader->upload($category->id, $menuThumbnail);
            }
        }
    }
}
