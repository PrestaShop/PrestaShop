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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Category\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Category\Exception\CategoryConstraintException;

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
    private function setDescriptions($descriptions)
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
     */
    private function setIsActive($isActive)
    {
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
     */
    private function setParentCategoryId($parentCategoryId)
    {
        $this->parentCategoryId = $parentCategoryId;
        
        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getMetaTitles()
    {
        return $this->metaTitles;
    }

    /**
     * @param array|string[] $metaTitles
     * 
     * @return self
     */
    private function setMetaTitles(array $metaTitles)
    {
        $this->metaTitles = $metaTitles;
        
        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getMetaDescriptions()
    {
        return $this->metaDescriptions;
    }

    /**
     * @param array|string[] $metaDescriptions
     * 
     * @return self
     */
    private function setMetaDescriptions(array $metaDescriptions)
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
    private function setMetaKeywords(array $metaKeywords)
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
     * @param array|int[] $associatedGroupIds
     * 
     * @return self
     */
    private function setAssociatedGroupIds(array $associatedGroupIds)
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
    private function setAssociatedShopIds(array $associatedShopIds)
    {
        $this->associatedShopIds = $associatedShopIds;

        return $this;
    }
}
