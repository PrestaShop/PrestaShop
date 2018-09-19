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

namespace PrestaShop\PrestaShop\Core\Domain\Category;

/**
 * Class EditableCategory
 */
class EditableCategory
{
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
     * @param mixed $thumbnailImage
     */
    public function __construct(
        array $name,
        $isActive,
        array $description,
        $parentId,  //@todo: should it be CategoryId?
        array $metaTitle,
        array $metaDescription,
        array $metaKeywords,
        array $linkRewrite,
        array $groupAssociationIds,
        array $shopAssociationIds,
        $thumbnailImage = null
    ) {
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
    public function getThumbnailImage()
    {
        return $this->thumbnailImage;
    }
}
