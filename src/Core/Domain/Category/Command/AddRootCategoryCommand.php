<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;

/**
 * Class AddRootCategoryCommand adds new root category.
 */
class AddRootCategoryCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var string[]
     */
    private $localizedLinkRewrites;

    /**
     * @var string[]
     */
    private $localizedDescriptions;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var string[]
     */
    private $localizedMetaTitles;

    /**
     * @var string[]
     */
    private $localizedMetaDescriptions;

    /**
     * @var string[]
     */
    private $localizedMetaKeywords;

    /**
     * @var int[]
     */
    private $associatedGroupIds;

    /**
     * @var int[]
     */
    private $associatedShopIds;

    /**
     * @param string[] $name
     * @param string[] $linkRewrite
     * @param bool $isActive
     *
     * @throws CategoryConstraintException
     */
    public function __construct(array $name, array $linkRewrite, $isActive)
    {
        $this
            ->setLocalizedNames($name)
            ->setLocalizedLinkRewrites($linkRewrite)
            ->setIsActive($isActive);
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @param string[] $localizedNames
     *
     * @return $this
     *
     * @throws CategoryConstraintException
     */
    public function setLocalizedNames(array $localizedNames)
    {
        if (empty($localizedNames)) {
            throw new CategoryConstraintException(
                'Category name cannot be empty',
                CategoryConstraintException::EMPTY_NAME
            );
        }

        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedLinkRewrites()
    {
        return $this->localizedLinkRewrites;
    }

    /**
     * @param string[] $localizedLinkRewrites
     *
     * @return $this
     *
     * @throws CategoryConstraintException
     */
    public function setLocalizedLinkRewrites(array $localizedLinkRewrites)
    {
        if (empty($localizedLinkRewrites)) {
            throw new CategoryConstraintException(
                'Category link rewrite cannot be empty',
                CategoryConstraintException::EMPTY_LINK_REWRITE
            );
        }

        $this->localizedLinkRewrites = $localizedLinkRewrites;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions()
    {
        return $this->localizedDescriptions;
    }

    /**
     * @param string[] $localizedDescriptions
     *
     * @return $this
     */
    public function setLocalizedDescriptions(array $localizedDescriptions)
    {
        $this->localizedDescriptions = $localizedDescriptions;

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
     * @return $this
     *
     * @throws CategoryConstraintException
     */
    public function setIsActive($isActive)
    {
        if (!is_bool($isActive)) {
            throw new CategoryConstraintException(
                'Invalid Category status supplied',
                CategoryConstraintException::INVALID_STATUS
            );
        }

        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaTitles()
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @param string[] $localizedMetaTitles
     *
     * @return $this
     */
    public function setLocalizedMetaTitles(array $localizedMetaTitles)
    {
        $this->localizedMetaTitles = $localizedMetaTitles;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaDescriptions()
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @param string[] $localizedMetaDescriptions
     *
     * @return $this
     */
    public function setLocalizedMetaDescriptions(array $localizedMetaDescriptions)
    {
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaKeywords()
    {
        return $this->localizedMetaKeywords;
    }

    /**
     * @param string[] $localizedMetaKeywords
     *
     * @return $this
     */
    public function setLocalizedMetaKeywords(array $localizedMetaKeywords)
    {
        $this->localizedMetaKeywords = $localizedMetaKeywords;

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
     * @param int[] $associatedGroupIds
     *
     * @return $this
     */
    public function setAssociatedGroupIds(array $associatedGroupIds)
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
     * @return $this
     */
    public function setAssociatedShopIds(array $associatedShopIds)
    {
        $this->associatedShopIds = $associatedShopIds;

        return $this;
    }
}
