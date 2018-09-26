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

namespace PrestaShop\PrestaShop\Core\Domain\Category\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class AbstractAddCategoryCommand defines common command for both simple and root category creation.
 */
abstract class AbstractRootCategoryCommand
{
    /**
     * @var string[]
     */
    private $name;

    /**
     * @var string[]
     */
    private $linkRewrite;

    /**
     * @var string[]
     */
    private $description;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var string[]
     */
    private $metaTitle;

    /**
     * @var string[]
     */
    private $metaDescription;

    /**
     * @var string[]
     */
    private $metaKeywords;

    /**
     * @var int[]
     */
    private $associatedGroupIds;

    /**
     * @var int[]
     */
    private $associatedShopIds;

    /**
     * @var UploadedFile|null
     */
    private $coverImage;

    /**
     * @var UploadedFile|null
     */
    private $thumbnailImage;

    /**
     * @var UploadedFile[]
     */
    private $menuThumbnailImages = [];

    /**
     * @return string[]
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string[] $name
     *
     * @return $this
     *
     * @throws CategoryConstraintException
     */
    public function setName(array $name)
    {
        if (empty($name)) {
            throw new CategoryConstraintException(
                'Category name cannot be empty',
                CategoryConstraintException::EMPTY_NAME
            );
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLinkRewrite()
    {
        return $this->linkRewrite;
    }

    /**
     * @param string[] $linkRewrite
     *
     * @return $this
     *
     * @throws CategoryConstraintException
     */
    public function setLinkRewrite(array $linkRewrite)
    {
        if (empty($linkRewrite)) {
            throw new CategoryConstraintException(
                'Category link rewrite cannot be empty',
                CategoryConstraintException::EMPTY_LINK_REWRITE
            );
        }

        $this->linkRewrite = $linkRewrite;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string[] $description
     *
     * @return $this
     */
    public function setDescription(array $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     *
     * @return $this
     *
     * @throws CategoryConstraintException
     */
    public function setIsActive($isActive)
    {
        if (!is_bool($isActive)) {
            throw new CategoryConstraintException(
                'Invalid Category status supplied',
                CategoryConstraintException::INVALID_STATUS
            );
        }

        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param string[] $metaTitle
     *
     * @return $this
     */
    public function setMetaTitle(array $metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string[] $metaDescription
     *
     * @return $this
     */
    public function setMetaDescription(array $metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @param string[] $metaKeywords
     *
     * @return $this
     */
    public function setMetaKeywords(array $metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getAssociatedGroupIds()
    {
        return $this->associatedGroupIds;
    }

    /**
     * @param int[] $associatedGroupIds
     *
     * @return $this
     */
    public function setAssociatedGroupIds(array $associatedGroupIds)
    {
        $this->associatedGroupIds = $associatedGroupIds;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getAssociatedShopIds()
    {
        return $this->associatedShopIds;
    }

    /**
     * @param int[] $associatedShopIds
     *
     * @return $this
     */
    public function setAssociatedShopIds(array $associatedShopIds)
    {
        $this->associatedShopIds = $associatedShopIds;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getCoverImage()
    {
        return $this->coverImage;
    }

    /**
     * @param UploadedFile $coverImage
     *
     * @return $this
     */
    public function setCoverImage(UploadedFile $coverImage)
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getThumbnailImage()
    {
        return $this->thumbnailImage;
    }

    /**
     * @param UploadedFile $thumbnailImage
     *
     * @return $this
     */
    public function setThumbnailImage(UploadedFile $thumbnailImage)
    {
        $this->thumbnailImage = $thumbnailImage;

        return $this;
    }

    /**
     * @return UploadedFile[]
     */
    public function getMenuThumbnailImages()
    {
        return $this->menuThumbnailImages;
    }

    /**
     * @param UploadedFile[] $menuThumbnailImages
     *
     * @return $this
     */
    public function setMenuThumbnailImages(array $menuThumbnailImages)
    {
        $this->menuThumbnailImages = $menuThumbnailImages;

        return $this;
    }
}
