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
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Category\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryHandler\GetCategoryForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;

/**
 * Class GetCategoryForEditingHandler
 */
final class GetCategoryForEditingHandler implements GetCategoryForEditingHandlerInterface
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @param LegacyContext $legacyContext
     */
    public function __construct(LegacyContext $legacyContext)
    {
        $this->legacyContext = $legacyContext;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CategoryNotFoundException
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
            $this->getCoverImage($query->getCategoryId()),
            $this->getThumbnailImage($query->getCategoryId())
        );

        return $editableCategory;
    }

    /**
     * @param CategoryId $categoryId
     *
     * @return array
     */
    private function getCoverImage(CategoryId $categoryId)
    {
        $imageType = 'jpg';
        $image = _PS_CAT_IMG_DIR_ . $categoryId->getValue() . '.' . $imageType;

        $imageTag = ImageManager::thumbnail(
            $image,
            'category' . '_' . $categoryId->getValue() . '.' . $imageType,
            350,
            $imageType,
            true,
            true
        );

        $imageSize = file_exists($image) ? filesize($image) / 1000 : '';

        $replacement = 'src="' . $this->legacyContext->getRootUrl();
        $imageTag = preg_replace('/src="(\\.\\.\\/)+/', $replacement, $imageTag);

        preg_match( '@src="([^"]+)"@' , $imageTag, $path);

        return [
            'size' => sprintf('%skb', $imageSize),
            'path' => $path[1],
        ];
    }

    /**
     * @param CategoryId $categoryId
     *
     * @return array
     */
    private function getThumbnailImage(CategoryId $categoryId)
    {
        $image = _PS_CAT_IMG_DIR_ . $categoryId->getValue() . '.jpg';
        $imageTypes = ImageType::getImagesTypes('categories');

        $thumb = '';
        $formatted_small = ImageType::getFormattedName('small');
        foreach ($imageTypes as $k => $image_type) {
            if ($formatted_small == $image_type['name']) {
                $thumb = _PS_CAT_IMG_DIR_ . $categoryId->getValue() . '-' . $image_type['name'] . '.jpg';
                if (is_file($thumb)) {
                    $imageTag = ImageManager::thumbnail(
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
            $imageTag = ImageManager::thumbnail($image, 'category_' . $categoryId->getValue() . '-thumb.jpg', 125, 'jpg', true, true);
            ImageManager::resize(_PS_TMP_IMG_DIR_ . 'category_' . $categoryId->getValue() . '-thumb.jpg', _PS_TMP_IMG_DIR_  . 'category_' . $categoryId->getValue() . '-thumb.jpg', (int) $image_type['width'], (int) $image_type['height']);
        }

        $thumbSize = file_exists($thumb) ? filesize($thumb) / 1000 : false;

        $replacement = 'src="' . $this->legacyContext->getRootUrl();
        $imageTag = preg_replace('/src="(\\.\\.\\/)+/', $replacement, $imageTag);

        preg_match( '@src="([^"]+)"@' , $imageTag, $path);

        return [
            'size' => sprintf('%skb', $thumbSize),
            'path' => $path[1],
        ];
    }
}
