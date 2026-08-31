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
    private CmsPageId $cmsPageId;

    private CmsPageCategoryId $cmsPageCategoryId;

    /**
     * @var string[]
     */
    private array $localizedTitle;

    /**
     * @var string[]
     */
    private array $localizedMetaTitle;

    /**
     * @var string[]
     */
    private array $localizedMetaDescription;

    /**
     * @var string[]
     */
    private array $localizedFriendlyUrl;

    /**
     * @var string[]
     */
    private array $localizedContent;

    private bool $indexedForSearch;

    private bool $displayed;

    private array $shopAssociation;

    /**
     * Url for opening FO page on save and preview action
     */
    private string $previewUrl;

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
        int $cmsPageId,
        int $cmsPageCategoryId,
        array $localizedTitle,
        array $localizedMetaTitle,
        array $localizedMetaDescription,
        array $localizedFriendlyUrl,
        array $localizedContent,
        bool $indexedForSearch,
        bool $displayed,
        array $shopAssociation,
        string $previewUrl
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

    public function getCmsPageId(): CmsPageId
    {
        return $this->cmsPageId;
    }

    public function getCmsPageCategoryId(): CmsPageCategoryId
    {
        return $this->cmsPageCategoryId;
    }

    /**
     * @return string[]
     */
    public function getLocalizedTitle(): array
    {
        return $this->localizedTitle;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaTitle(): array
    {
        return $this->localizedMetaTitle;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaDescription(): array
    {
        return $this->localizedMetaDescription;
    }

    /**
     * @return string[]
     */
    public function getLocalizedFriendlyUrl(): array
    {
        return $this->localizedFriendlyUrl;
    }

    /**
     * @return string[]
     */
    public function getLocalizedContent(): array
    {
        return $this->localizedContent;
    }

    public function isIndexedForSearch(): bool
    {
        return $this->indexedForSearch;
    }

    public function isDisplayed(): bool
    {
        return $this->displayed;
    }

    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }

    public function getPreviewUrl(): string
    {
        return $this->previewUrl;
    }
}
