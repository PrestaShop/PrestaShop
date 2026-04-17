<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\Command;

use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\ValueObject\CmsPageId;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Edits cms page
 */
class EditCmsPageCommand
{
    /**
     * @var CmsPageId
     */
    private $cmsPageId;

    /**
     * @var CmsPageCategoryId|null
     */
    private $cmsPageCategoryId;

    /**
     * @var string[]|null
     */
    private $localizedTitle;

    /**
     * @var string[]|null
     */
    private $localizedMetaTitle;

    /**
     * @var string[]|null
     */
    private $localizedMetaDescription;

    /**
     * @var string[]|null
     */
    private $localizedFriendlyUrl;

    /**
     * @var string[]|null
     */
    private $localizedContent;

    /**
     * @var bool|null
     */
    private $isIndexedForSearch;

    /**
     * @var bool|null
     */
    private $isDisplayed;

    /**
     * @var array|null
     */
    private $shopAssociation;

    /**
     * @param int $cmsPageId
     *
     * @throws CmsPageException
     */
    public function __construct($cmsPageId)
    {
        $this->cmsPageId = new CmsPageId($cmsPageId);
    }

    /**
     * @return CmsPageId
     */
    public function getCmsPageId()
    {
        return $this->cmsPageId;
    }

    /**
     * @return CmsPageCategoryId|null
     */
    public function getCmsPageCategoryId()
    {
        return $this->cmsPageCategoryId;
    }

    /**
     * @param int|null $cmsPageCategoryId
     *
     * @return self
     *
     * @throws CmsPageCategoryException
     */
    public function setCmsPageCategoryId($cmsPageCategoryId)
    {
        $this->cmsPageCategoryId = new CmsPageCategoryId($cmsPageCategoryId);

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedTitle()
    {
        return $this->localizedTitle;
    }

    /**
     * @param string[] $localizedTitle
     *
     * @return self
     */
    public function setLocalizedTitle(array $localizedTitle)
    {
        $this->localizedTitle = $localizedTitle;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaTitle()
    {
        return $this->localizedMetaTitle;
    }

    /**
     * @param string[] $localizedMetaTitle
     *
     * @return self
     */
    public function setLocalizedMetaTitle(array $localizedMetaTitle)
    {
        $this->localizedMetaTitle = $localizedMetaTitle;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaDescription()
    {
        return $this->localizedMetaDescription;
    }

    /**
     * @param string[] $localizedMetaDescription
     *
     * @return self
     */
    public function setLocalizedMetaDescription(array $localizedMetaDescription)
    {
        $this->localizedMetaDescription = $localizedMetaDescription;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedFriendlyUrl()
    {
        return $this->localizedFriendlyUrl;
    }

    /**
     * @param string[] $localizedFriendlyUrl
     *
     * @return self
     */
    public function setLocalizedFriendlyUrl(array $localizedFriendlyUrl)
    {
        $this->localizedFriendlyUrl = $localizedFriendlyUrl;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedContent()
    {
        return $this->localizedContent;
    }

    /**
     * @param string[] $localizedContent
     *
     * @return self
     */
    public function setLocalizedContent(array $localizedContent)
    {
        $this->localizedContent = $localizedContent;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isIndexedForSearch()
    {
        return $this->isIndexedForSearch;
    }

    /**
     * @param bool|null $isIndexedForSearch
     *
     * @return self
     */
    public function setIsIndexedForSearch($isIndexedForSearch)
    {
        $this->isIndexedForSearch = $isIndexedForSearch;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isDisplayed()
    {
        return $this->isDisplayed;
    }

    /**
     * @param bool|null $isDisplayed
     *
     * @return self
     */
    public function setIsDisplayed($isDisplayed)
    {
        $this->isDisplayed = $isDisplayed;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }

    /**
     * @param array|null $shopAssociation
     *
     * @return self
     */
    public function setShopAssociation(?array $shopAssociation = null)
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }
}
