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
 * Class AddCmsPageCategoryCommand is responsible for adding cms page category.
 */
class AddCmsPageCategoryCommand extends AbstractCmsPageCategoryCommand
{
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
     * @param array $localisedName
     * @param array $localisedFriendlyUrl
     * @param int $parentId
     * @param bool $isDisplayed
     *
     * @throws CmsPageCategoryException
     */
    public function __construct(
        array $localisedName,
        array $localisedFriendlyUrl,
        $parentId,
        $isDisplayed
    ) {
        $this->assertCategoryName($localisedName);

        $this->localisedName = $localisedName;
        $this->localisedFriendlyUrl = $localisedFriendlyUrl;
        $this->parentId = new CmsPageCategoryId($parentId);
        $this->isDisplayed = $isDisplayed;
    }

    /**
     * @return array
     */
    public function getLocalisedName()
    {
        return $this->localisedName;
    }

    /**
     * @return array
     */
    public function getLocalisedFriendlyUrl()
    {
        return $this->localisedFriendlyUrl;
    }

    /**
     * @return CmsPageCategoryId
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->isDisplayed;
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
