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

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\Command;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Adds new cms page
 */
class AddCmsPageCommand
{
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
     * @param int $cmsPageCategoryId
     * @param string[] $localizedTitle
     * @param string[] $localizedMetaTitle
     * @param string[] $localizedMetaDescription
     * @param string[] $LocalizedMetaKeyword
     * @param string[] $localizedFriendlyUrl
     * @param string[] $localizedContent
     * @param bool $indexedForSearch
     * @param bool $displayed
     * @param array $shopAssociation
     *
     * @throws CmsPageCategoryException
     */
    public function __construct(
        $cmsPageCategoryId,
        array $localizedTitle,
        array $localizedMetaTitle,
        array $localizedMetaDescription,
        array $LocalizedMetaKeyword,
        array $localizedFriendlyUrl,
        array $localizedContent,
        $indexedForSearch,
        $displayed,
        array $shopAssociation
    ) {
        $this->cmsPageCategoryId = new CmsPageCategoryId($cmsPageCategoryId);
        $this->localizedTitle = $localizedTitle;
        $this->localizedMetaTitle = $localizedMetaTitle;
        $this->localizedMetaDescription = $localizedMetaDescription;
        $this->localizedFriendlyUrl = $localizedFriendlyUrl;
        $this->localizedContent = $localizedContent;
        $this->indexedForSearch = $indexedForSearch;
        $this->displayed = $displayed;
        $this->shopAssociation = $shopAssociation;
    }

    /**
     * @return CmsPageCategoryId
     */
    public function getCmsPageCategory()
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
}
