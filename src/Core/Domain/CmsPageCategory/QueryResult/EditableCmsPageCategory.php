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
    private $localisedMetaKeywords;

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
     * @param array $localisedMetaKeywords
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
        array $localisedMetaKeywords,
        array $metaTitle,
        array $localisedFriendlyUrl,
        array $shopIds
    ) {
        $this->localisedName = $localisedName;
        $this->isDisplayed = $isDisplayed;
        $this->parentId = new CmsPageCategoryId($parentId);
        $this->localisedDescription = $localisedDescription;
        $this->localisedMetaDescription = $localisedMetaDescription;
        $this->localisedMetaKeywords = $localisedMetaKeywords;
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
    public function getLocalisedMetaKeywords()
    {
        return $this->localisedMetaKeywords;
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
