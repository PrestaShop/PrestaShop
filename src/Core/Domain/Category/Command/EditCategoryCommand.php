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

namespace PrestaShop\PrestaShop\Core\Domain\Category\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class EditCategoryCommand edits given category.
 */
class EditCategoryCommand
{
    /**
     * @var CategoryId
     */
    private $categoryId;

    /**
     * @var int
     */
    private $parentCategoryId;

    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var string[]
     */
    private $localizedLinkRewrites;

    /**
     * @var string[]
     */
    private $localizedDescriptions;

    /**
     * @var string[]|null
     */
    private $localizedAdditionalDescriptions;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var string[]
     */
    private $localizedMetaTitles;

    /**
     * @var string[]
     */
    private $localizedMetaDescriptions;

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
     * @var array
     */
    private $menuThumbnailImages;

    /**
     * @param int $categoryId
     */
    public function __construct($categoryId)
    {
        $this->categoryId = new CategoryId($categoryId);
        $this->menuThumbnailImages = [];
    }

    /**
     * @return CategoryId
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @return int
     */
    public function getParentCategoryId()
    {
        return $this->parentCategoryId;
    }

    /**
     * @param int $parentCategoryId
     *
     * @return self
     *
     * @throws CategoryConstraintException
     */
    public function setParentCategoryId($parentCategoryId)
    {
        if (!is_numeric($parentCategoryId) || 0 >= $parentCategoryId) {
            throw new CategoryConstraintException(sprintf('Invalid Category parent id %s supplied', var_export($parentCategoryId, true)), CategoryConstraintException::INVALID_PARENT_ID);
        }

        if ($this->categoryId->isEqual(new CategoryId((int) $parentCategoryId))) {
            throw new CategoryConstraintException('Category cannot be parent of itself.');
        }

        $this->parentCategoryId = $parentCategoryId;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @param string[] $localizedNames
     *
     * @return $this
     *
     * @throws CategoryConstraintException
     */
    public function setLocalizedNames(array $localizedNames)
    {
        if (empty($localizedNames)) {
            throw new CategoryConstraintException('Category name cannot be empty', CategoryConstraintException::EMPTY_NAME);
        }

        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedLinkRewrites()
    {
        return $this->localizedLinkRewrites;
    }

    /**
     * @param string[] $localizedLinkRewrites
     *
     * @return $this
     *
     * @throws CategoryConstraintException
     */
    public function setLocalizedLinkRewrites(array $localizedLinkRewrites)
    {
        if (empty($localizedLinkRewrites)) {
            throw new CategoryConstraintException('Category link rewrite cannot be empty', CategoryConstraintException::EMPTY_LINK_REWRITE);
        }

        $this->localizedLinkRewrites = $localizedLinkRewrites;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions()
    {
        return $this->localizedDescriptions;
    }

    /**
     * @param string[] $localizedDescriptions
     *
     * @return $this
     */
    public function setLocalizedDescriptions(array $localizedDescriptions)
    {
        $this->localizedDescriptions = $localizedDescriptions;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedAdditionalDescriptions(): ?array
    {
        return $this->localizedAdditionalDescriptions;
    }

    /**
     * @param string[] $localizedAdditionalDescriptions
     *
     * @return $this
     */
    public function setLocalizedAdditionalDescriptions(array $localizedAdditionalDescriptions): self
    {
        $this->localizedAdditionalDescriptions = $localizedAdditionalDescriptions;

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
            throw new CategoryConstraintException('Invalid Category status supplied', CategoryConstraintException::INVALID_STATUS);
        }

        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaTitles()
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @param string[] $localizedMetaTitles
     *
     * @return $this
     */
    public function setLocalizedMetaTitles(array $localizedMetaTitles)
    {
        $this->localizedMetaTitles = $localizedMetaTitles;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaDescriptions()
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @param string[] $localizedMetaDescriptions
     *
     * @return $this
     */
    public function setLocalizedMetaDescriptions(array $localizedMetaDescriptions)
    {
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;

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
     * @return UploadedFile|null
     */
    public function getCoverImage(): ?UploadedFile
    {
        return $this->coverImage;
    }

    /**
     * @param UploadedFile|null $coverImage
     */
    public function setCoverImage(?UploadedFile $coverImage): void
    {
        $this->coverImage = $coverImage;
    }

    /**
     * @return UploadedFile|null
     */
    public function getThumbnailImage(): ?UploadedFile
    {
        return $this->thumbnailImage;
    }

    /**
     * @param UploadedFile|null $thumbnailImage
     */
    public function setThumbnailImage(?UploadedFile $thumbnailImage): void
    {
        $this->thumbnailImage = $thumbnailImage;
    }

    /**
     * @return array
     */
    public function getMenuThumbnailImages(): array
    {
        return $this->menuThumbnailImages;
    }

    /**
     * @param array $menuThumbnailImages
     */
    public function setMenuThumbnailImages(array $menuThumbnailImages): void
    {
        $this->menuThumbnailImages = $menuThumbnailImages;
    }
}
