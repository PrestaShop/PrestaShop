<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\ValueObject\CmsPageId;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Transfers cms page data for editing
 */
class EditableCmsPage
{
    /**
     * @var CmsPageId
     */
    private $cmsPageId;

    /**
     * @var CmsPageCategoryId
     */
    private $cmsPageCategoryId;

    /**
     * @var string[]
     */
    private $localizedTitle;

    /**
     * @var string[]
     */
    private $localizedMetaTitle;

    /**
     * @var string[]
     */
    private $localizedMetaDescription;

    /**
     * @var string[]
     */
    private $localizedFriendlyUrl;

    /**
     * @var string[]
     */
    private $localizedContent;

    /**
     * @var bool
     */
    private $indexedForSearch;

    /**
     * @var bool
     */
    private $displayed;

    /**
     * @var array
     */
    private $shopAssociation;

    /**
     * Url for opening FO page on save and preview action
     *
     * @var string
     */
    private $previewUrl;

    /**
     * @param int $cmsPageId
     * @param int $cmsPageCategoryId
     * @param string[] $localizedTitle
     * @param string[] $localizedMetaTitle
     * @param string[] $localizedMetaDescription
     * @param string[] $localizedFriendlyUrl
     * @param string[] $localizedContent
     * @param bool $indexedForSearch
     * @param bool $displayed
     * @param array $shopAssociation
     * @param string $previewUrl
     *
     * @throws CmsPageCategoryException
     * @throws CmsPageException
     */
    public function __construct(
        $cmsPageId,
        $cmsPageCategoryId,
        array $localizedTitle,
        array $localizedMetaTitle,
        array $localizedMetaDescription,
        array $localizedFriendlyUrl,
        array $localizedContent,
        $indexedForSearch,
        $displayed,
        array $shopAssociation,
        $previewUrl
    ) {
        $this->cmsPageId = new CmsPageId($cmsPageId);
        $this->cmsPageCategoryId = new CmsPageCategoryId($cmsPageCategoryId);
        $this->localizedTitle = $localizedTitle;
        $this->localizedMetaTitle = $localizedMetaTitle;
        $this->localizedMetaDescription = $localizedMetaDescription;
        $this->localizedFriendlyUrl = $localizedFriendlyUrl;
        $this->localizedContent = $localizedContent;
        $this->indexedForSearch = $indexedForSearch;
        $this->displayed = $displayed;
        $this->shopAssociation = $shopAssociation;
        $this->previewUrl = $previewUrl;
    }

    /**
     * @return CmsPageId
     */
    public function getCmsPageId()
    {
        return $this->cmsPageId;
    }

    /**
     * @return CmsPageCategoryId
     */
    public function getCmsPageCategoryId()
    {
        return $this->cmsPageCategoryId;
    }

    /**
     * @return string[]
     */
    public function getLocalizedTitle()
    {
        return $this->localizedTitle;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaTitle()
    {
        return $this->localizedMetaTitle;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaDescription()
    {
        return $this->localizedMetaDescription;
    }

    /**
     * @return string[]
     */
    public function getLocalizedFriendlyUrl()
    {
        return $this->localizedFriendlyUrl;
    }

    /**
     * @return string[]
     */
    public function getLocalizedContent()
    {
        return $this->localizedContent;
    }

    /**
     * @return bool
     */
    public function isIndexedForSearch()
    {
        return $this->indexedForSearch;
    }

    /**
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->displayed;
    }

    /**
     * @return array
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }

    /**
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->previewUrl;
    }
}
