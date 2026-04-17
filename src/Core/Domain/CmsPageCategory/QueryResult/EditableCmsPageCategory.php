<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Class EditableCmsPageCategory
 */
class EditableCmsPageCategory
{
    /**
     * @var array
     */
    private $localisedName;

    /**
     * @var bool
     */
    private $isDisplayed;

    /**
     * @var CmsPageCategoryId
     */
    private $parentId;

    /**
     * @var array
     */
    private $localisedDescription;

    /**
     * @var array
     */
    private $localisedMetaDescription;

    /**
     * @var array
     */
    private $localisedFriendlyUrl;
    /**
     * @var array
     */
    private $metaTitle;
    /**
     * @var array
     */
    private $shopIds;

    /**
     * @param array $localisedName
     * @param bool $isDisplayed
     * @param int $parentId
     * @param array $localisedDescription
     * @param array $localisedMetaDescription
     * @param array $metaTitle
     * @param array $localisedFriendlyUrl
     * @param array $shopIds
     *
     * @throws CmsPageCategoryException
     */
    public function __construct(
        array $localisedName,
        $isDisplayed,
        $parentId,
        array $localisedDescription,
        array $localisedMetaDescription,
        array $metaTitle,
        array $localisedFriendlyUrl,
        array $shopIds
    ) {
        $this->localisedName = $localisedName;
        $this->isDisplayed = $isDisplayed;
        $this->parentId = new CmsPageCategoryId($parentId);
        $this->localisedDescription = $localisedDescription;
        $this->localisedMetaDescription = $localisedMetaDescription;
        $this->localisedFriendlyUrl = $localisedFriendlyUrl;
        $this->metaTitle = $metaTitle;
        $this->shopIds = $shopIds;
    }

    /**
     * @return array
     */
    public function getLocalisedName()
    {
        return $this->localisedName;
    }

    /**
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->isDisplayed;
    }

    /**
     * @return CmsPageCategoryId
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @return array
     */
    public function getLocalisedDescription()
    {
        return $this->localisedDescription;
    }

    /**
     * @return array
     */
    public function getLocalisedMetaDescription()
    {
        return $this->localisedMetaDescription;
    }

    /**
     * @return array
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @return array
     */
    public function getLocalisedFriendlyUrl()
    {
        return $this->localisedFriendlyUrl;
    }

    /**
     * @return array
     */
    public function getShopIds()
    {
        return $this->shopIds;
    }
}
