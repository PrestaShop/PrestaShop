<?php
/**
 * 2007-2018 PrestaShop
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
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\AddCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;

/**
 * Class AddCategoryHandler
 *
 * @internal
 */
final class AddCategoryHandler implements AddCategoryHandlerInterface
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
     * @param ImageUploaderInterface $categoryCoverUploader
     * @param ImageUploaderInterface $categoryThumbnailUploader
     */
    public function __construct(
        ImageUploaderInterface $categoryCoverUploader,
        ImageUploaderInterface $categoryThumbnailUploader
    ) {
        $this->categoryImageUploader = $categoryCoverUploader;
        $this->categoryThumbnailUploader = $categoryThumbnailUploader;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddCategoryCommand $command)
    {
        $category = new Category();
        $category->name = $command->getNames();
        $category->link_rewrite = $command->getLinkRewrites();
        $category->description = $command->getDescriptions();
        $category->id_parent = $command->getParentCategoryId();
        $category->meta_title = $command->getMetaTitles();
        $category->meta_description = $command->getMetaDescriptions();
        $category->meta_keywords = $command->getMetaKeywords();
        $category->groupBox = $command->getAssociatedGroupIds();

        // inside Category::add() it checks if shop association is submitted by
        // by retrieving data from $_POST["checkBoxShopAsso_category"]
        $_POST['checkBoxShopAsso_category'] = $command->getAssociatedShopIds();

        $category->add();

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
    }
}
