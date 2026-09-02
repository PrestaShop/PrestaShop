<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
