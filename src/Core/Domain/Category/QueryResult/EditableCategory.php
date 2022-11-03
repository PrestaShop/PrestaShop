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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\MenuThumbnailId;

/**
 * Stores category data needed for editing.
 */
class EditableCategory
{
    /**
     * @var CategoryId
     */
    private $id;

    /**
     * @var string[]
     */
    private $name;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var string[]
     */
    private $description;

    /**
     * @var int
     */
    private $parentId;

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
     * @var string[]
     */
    private $linkRewrite;

    /**
     * @var int[]
     */
    private $groupAssociationIds;

    /**
     * @var int[]
     */
    private $shopAssociationIds;

    /**
     * @var mixed
     */
    private $thumbnailImage;

    /**
     * @var null
     */
    private $coverImage;

    /**
     * @var array
     */
    private $menuThumbnailImages;

    /**
     * @var bool
     */
    private $isRootCategory;

    /**
     * @var array
     */
    private $subCategories;

    /**
     * @var string[]
     */
    private $additionalDescription;

    /**
     * @param CategoryId $id
     * @param string[] $name
     * @param bool $isActive
     * @param string[] $description
     * @param int $parentId
     * @param string[] $metaTitle
     * @param string[] $metaDescription
     * @param string[] $metaKeywords
     * @param string[] $linkRewrite
     * @param int[] $groupAssociationIds
     * @param int[] $shopAssociationIds
     * @param bool $isRootCategory
     * @param mixed $coverImage
     * @param mixed $thumbnailImage
     * @param array $menuThumbnailImages
     * @param array $subCategories
     * @param string[] $additionalDescription
     */
    public function __construct(
        CategoryId $id,
        array $name,
        $isActive,
        array $description,
        $parentId,
        array $metaTitle,
        array $metaDescription,
        array $metaKeywords,
        array $linkRewrite,
        array $groupAssociationIds,
        array $shopAssociationIds,
        $isRootCategory,
        $coverImage = null,
        $thumbnailImage = null,
        array $menuThumbnailImages = [],
        array $subCategories = [],
        array $additionalDescription = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->isActive = $isActive;
        $this->description = $description;
        $this->parentId = $parentId;
        $this->metaTitle = $metaTitle;
        $this->metaDescription = $metaDescription;
        $this->metaKeywords = $metaKeywords;
        $this->linkRewrite = $linkRewrite;
        $this->groupAssociationIds = $groupAssociationIds;
        $this->shopAssociationIds = $shopAssociationIds;
        $this->thumbnailImage = $thumbnailImage;
        $this->coverImage = $coverImage;
        $this->menuThumbnailImages = $menuThumbnailImages;
        $this->isRootCategory = $isRootCategory;
        $this->subCategories = $subCategories;
        $this->additionalDescription = $additionalDescription;
    }

    /**
     * @return CategoryId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return string[]
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string[]
     */
    public function getAdditionalDescription(): array
    {
        return $this->additionalDescription;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @return string[]
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @return string[]
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @return string[]
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @return string[]
     */
    public function getLinkRewrite()
    {
        return $this->linkRewrite;
    }

    /**
     * @return int[]
     */
    public function getGroupAssociationIds()
    {
        return $this->groupAssociationIds;
    }

    /**
     * @return int[]
     */
    public function getShopAssociationIds()
    {
        return $this->shopAssociationIds;
    }

    /**
     * @return mixed
     */
    public function getCoverImage()
    {
        return $this->coverImage;
    }

    /**
     * @return mixed
     */
    public function getThumbnailImage()
    {
        return $this->thumbnailImage;
    }

    /**
     * @return array
     */
    public function getMenuThumbnailImages()
    {
        return $this->menuThumbnailImages;
    }

    /**
     * @return bool
     */
    public function isRootCategory()
    {
        return $this->isRootCategory;
    }

    /**
     * @return bool
     */
    public function canContainMoreMenuThumbnails()
    {
        return count($this->getMenuThumbnailImages()) < count(MenuThumbnailId::ALLOWED_ID_VALUES);
    }

    /**
     * @return array
     */
    public function getSubCategories()
    {
        return $this->subCategories;
    }
}
