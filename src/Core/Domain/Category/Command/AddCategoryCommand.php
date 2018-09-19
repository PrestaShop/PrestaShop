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
 * Class AddCategoryCommand adds new category
 */
class AddCategoryCommand
{
    /**
     * @var string[]
     */
    private $names;

    /**
     * @var string[]
     */
    private $linkRewrites;

    /**
     * @var string[]
     */
    private $descriptions;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var int
     */
    private $parentCategoryId;

    /**
     * @var string[]
     */
    private $metaTitles;

    /**
     * @var string[]
     */
    private $metaDescriptions;

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
     * @param string[] $names
     * @param string[] $linkRewrites
     * @param string[] $descriptions
     * @param bool $isActive
     * @param int $parentCategoryId
     * @param string[] $metaTitles
     * @param string[] $metaDescriptions
     * @param string[] $metaKeywords
     * @param int[] $associatedGroupIds
     * @param int[] $associatedShopIds
     *
     * @throws CategoryConstraintException
     */
    public function __construct(
        array $names,
        array $linkRewrites,
        $parentCategoryId,
        $isActive = true,
        array $descriptions = [],
        array $metaTitles = [],
        array $metaDescriptions = [],
        array $metaKeywords = [],
        array $associatedGroupIds = [],
        array $associatedShopIds = []
    ) {
        $this
            ->setNames($names)
            ->setLinkRewrites($linkRewrites)
            ->setParentCategoryId($parentCategoryId)
            ->setIsActive($isActive)
            ->setDescriptions($descriptions)
            ->setMetaTitles($metaTitles)
            ->setMetaDescriptions($metaDescriptions)
            ->setMetaKeywords($metaKeywords)
            ->setAssociatedGroupIds($associatedGroupIds)
            ->setAssociatedShopIds($associatedShopIds)
        ;
    }

    /**
     * @return string[]
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @param string[] $names
     *
     * @return self
     *
     * @throws CategoryConstraintException
     */
    private function setNames($names)
    {
        if (empty($names)) {
            throw new CategoryConstraintException(
                'Category name cannot be empty',
                CategoryConstraintException::EMPTY_NAME
            );
        }

        $this->names = $names;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLinkRewrites()
    {
        return $this->linkRewrites;
    }

    /**
     * @param string[] $linkRewrites
     *
     * @return self
     *
     * @throws CategoryConstraintException
     */
    private function setLinkRewrites(array $linkRewrites)
    {
        if (empty($linkRewrites)) {
            throw new CategoryConstraintException(
                'Category link rewrite cannot be empty',
                CategoryConstraintException::EMPTY_LINK_REWRITE
            );
        }

        $this->linkRewrites = $linkRewrites;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDescriptions()
    {
        return $this->descriptions;
    }

    /**
     * @param string[] $descriptions
     * 
     * @return self
     */
    public function setDescriptions(array $descriptions)
    {
        $this->descriptions = $descriptions;

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
     * @return self
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
    private function setParentCategoryId($parentCategoryId)
    {
        if (!is_int($parentCategoryId) || 0 >= $parentCategoryId) {
            throw new CategoryConstraintException(
                sprintf('Invalid Category parent id %s supplied', var_export($parentCategoryId, true)),
                CategoryConstraintException::INVALID_PARENT_ID
            );
        }

        $this->parentCategoryId = $parentCategoryId;
        
        return $this;
    }

    /**
     * @return string[]
     */
    public function getMetaTitles()
    {
        return $this->metaTitles;
    }

    /**
     * @param string[] $metaTitles
     * 
     * @return self
     */
    public function setMetaTitles(array $metaTitles)
    {
        $this->metaTitles = $metaTitles;
        
        return $this;
    }

    /**
     * @return string[]
     */
    public function getMetaDescriptions()
    {
        return $this->metaDescriptions;
    }

    /**
     * @param string[] $metaDescriptions
     * 
     * @return self
     */
    public function setMetaDescriptions(array $metaDescriptions)
    {
        $this->metaDescriptions = $metaDescriptions;
        
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
     * @return self
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
     * @return self
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
     * @return self
     */
    public function setAssociatedShopIds(array $associatedShopIds)
    {
        $this->associatedShopIds = $associatedShopIds;

        return $this;
    }

    /**
     * @return UploadedFile|null
     */
    public function getThumbnailImage()
    {
        return $this->thumbnailImage;
    }

    /**
     * @param UploadedFile $thumbnailImage
     */
    public function setThumbnailImage(UploadedFile $thumbnailImage)
    {
        $this->thumbnailImage = $thumbnailImage;
    }

    /**
     * @return UploadedFile|null
     */
    public function getCoverImage()
    {
        return $this->coverImage;
    }

    /**
     * @param UploadedFile $coverImage
     */
    public function setCoverImage(UploadedFile $coverImage)
    {
        $this->coverImage = $coverImage;
    }
}
