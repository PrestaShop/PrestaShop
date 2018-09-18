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

namespace PrestaShop\PrestaShop\Adapter\Category\QueryHandler;

use Category;
use ImageManager;
use ImageType;
use PrestaShop\PrestaShop\Adapter\Category\Image\CategoryThumbnailRetriever;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\QueryHandler\GetCategoryForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\ValueObject\CategoryId;

/**
 * Class GetCategoryForEditingHandler
 */
final class GetCategoryForEditingHandler implements GetCategoryForEditingHandlerInterface
{
    /**
     * @var CategoryThumbnailRetriever
     */
    private $categoryThumbnailRetriever;

    /**
     * @param CategoryThumbnailRetriever $categoryThumbnailRetriever
     */
    public function __construct(CategoryThumbnailRetriever $categoryThumbnailRetriever)
    {
        $this->categoryThumbnailRetriever = $categoryThumbnailRetriever;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCategoryForEditing $query)
    {
        $category = new Category($query->getCategoryId()->getValue());

        if (!$category->id) {
            throw new CategoryNotFoundException(
                sprintf('Category with id "%s" was not found', $query->getCategoryId()->getValue())
            );
        }

        $editableCategory = new EditableCategory(
            $category->name,
            (bool) $category->active,
            $category->description,
            $category->id_parent,
            $category->meta_title,
            $category->meta_description,
            $category->meta_keywords,
            $category->link_rewrite,
            $category->getGroups(),
            $category->getAssociatedShops(),
            $this->getThumbnailImage($query->getCategoryId())
        );

        return $editableCategory;
    }

    private function getThumbnailImage(CategoryId $categoryId)
    {
        $image = _PS_CAT_IMG_DIR_ . $categoryId->getValue() . '.jpg';
        $image_url = ImageManager::thumbnail($image, 'category_' . (int) $categoryId->getValue() . '.jpg', 350, 'jpg', true, true);

        $images_types = ImageType::getImagesTypes('categories');
        $format = array();
        $thumb = $thumb_url = '';
        $formatted_category = ImageType::getFormattedName('category');
        $formatted_small = ImageType::getFormattedName('small');
        foreach ($images_types as $k => $image_type) {
            if ($formatted_category == $image_type['name']) {
                $format['category'] = $image_type;
            } elseif ($formatted_small == $image_type['name']) {
                $format['small'] = $image_type;
                $thumb = _PS_CAT_IMG_DIR_ . $categoryId->getValue() . '-' . $image_type['name'] . '.jpg';
                if (is_file($thumb)) {
                    $thumb_url = ImageManager::thumbnail(
                        $thumb,
                        'category_' . (int) $categoryId->getValue() . '-thumb.jpg',
                        (int) $image_type['width'],
                        'jpg',
                        true,
                        true
                    );
                }
            }
        }

        if (!is_file($thumb)) {
            $thumb = $image;
            $thumb_url = ImageManager::thumbnail($image, 'category_' . (int) $categoryId->getValue() . '-thumb.jpg', 125, 'jpg', true, true);
            ImageManager::resize(_PS_TMP_IMG_DIR_ . 'category_' . (int) $categoryId->getValue() . '-thumb.jpg', _PS_TMP_IMG_DIR_  . 'category_' . (int) $categoryId->getValue() . '-thumb.jpg', (int) $image_type['width'], (int) $image_type['height']);
        }

        $thumb_size = file_exists($thumb) ? filesize($thumb) / 1000 : false;

        return [
            'size' => $thumb_size,
            'path' => $this->categoryThumbnailRetriever->retrieve($categoryId->getValue()),
            'file' => new \SplFileInfo($thumb)
        ];
    }
}
