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

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Edits cms page category.
 */
class EditCmsPageCategoryCommand extends AbstractCmsPageCategoryCommand
{
    /**
     * @var CmsPageCategoryId
     */
    private $cmsPageCategoryId;

    /**
     * @var array
     */
    private $localisedName;

    /**
     * @var array
     */
    private $localisedFriendlyUrl;

    /**
     * @var CmsPageCategoryId
     */
    private $parentId;

    /**
     * @var bool
     */
    private $isDisplayed;

    /**
     * @var string[]
     */
    private $localisedDescription;

    /**
     * @var string[]
     */
    private $localisedMetaTitle;

    /**
     * @var string[]
     */
    private $localisedMetaDescription;

    /**
     * @var string[]
     */
    private $localisedMetaKeywords;

    /**
     * @var int[]
     */
    private $shopAssociation;

    /**
     * @param int $cmsPageCategoryId
     *
     * @throws CmsPageCategoryException
     */
    public function __construct($cmsPageCategoryId)
    {
        $this->cmsPageCategoryId = new CmsPageCategoryId($cmsPageCategoryId);
    }

    /**
     * @return CmsPageCategoryId
     */
    public function getCmsPageCategoryId()
    {
        return $this->cmsPageCategoryId;
    }

    /**
     * @return array
     */
    public function getLocalisedName()
    {
        return $this->localisedName;
    }

    /**
     * @param array $localisedName
     *
     * @return self
     *
     * @throws CmsPageCategoryConstraintException
     */
    public function setLocalisedName(array $localisedName)
    {
        $this->assertCategoryName($localisedName);
        $this->localisedName = $localisedName;

        return $this;
    }

    /**
     * @return array
     */
    public function getLocalisedFriendlyUrl()
    {
        return $this->localisedFriendlyUrl;
    }

    /**
     * @param array $localisedFriendlyUrl
     *
     * @return self
     */
    public function setLocalisedFriendlyUrl(array $localisedFriendlyUrl)
    {
        $this->localisedFriendlyUrl = $localisedFriendlyUrl;

        return $this;
    }

    /**
     * @return CmsPageCategoryId
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     *
     * @return self
     *
     * @throws CmsPageCategoryException
     */
    public function setParentId($parentId)
    {
        $this->parentId = new CmsPageCategoryId($parentId);

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->isDisplayed;
    }

    /**
     * @param bool $isDisplayed
     *
     * @return self
     */
    public function setIsDisplayed($isDisplayed)
    {
        $this->isDisplayed = $isDisplayed;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedDescription()
    {
        return $this->localisedDescription;
    }

    /**
     * @param string[] $localisedDescription
     *
     * @return self
     */
    public function setLocalisedDescription(array $localisedDescription)
    {
        $this->localisedDescription = $localisedDescription;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedMetaTitle()
    {
        return $this->localisedMetaTitle;
    }

    /**
     * @param string[] $localisedMetaTitle
     *
     * @return self
     *
     * @throws CmsPageCategoryConstraintException
     */
    public function setLocalisedMetaTitle(array $localisedMetaTitle)
    {
        $this->assertIsGenericNameForMetaTitle($localisedMetaTitle);
        $this->localisedMetaTitle = $localisedMetaTitle;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedMetaDescription()
    {
        return $this->localisedMetaDescription;
    }

    /**
     * @param string[] $localisedMetaDescription
     *
     * @return self
     *
     * @throws CmsPageCategoryConstraintException
     */
    public function setLocalisedMetaDescription(array $localisedMetaDescription)
    {
        $this->assertIsGenericNameForMetaDescription($localisedMetaDescription);
        $this->localisedMetaDescription = $localisedMetaDescription;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedMetaKeywords()
    {
        return $this->localisedMetaKeywords;
    }

    /**
     * @param string[] $localisedMetaKeywords
     *
     * @return self
     *
     * @throws CmsPageCategoryConstraintException
     */
    public function setLocalisedMetaKeywords(array $localisedMetaKeywords)
    {
        $this->assertIsGenericNameForMetaKeywords($localisedMetaKeywords);
        $this->localisedMetaKeywords = $localisedMetaKeywords;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }

    /**
     * @param int[] $shopAssociation
     *
     * @return self
     */
    public function setShopAssociation(array $shopAssociation)
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }
}
