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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Category\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;

/**
 * Class EditRootCategoryCommand edits given root category.
 */
class AbstractEditCategoryCommand
{
    /**
     * @var CategoryId
     */
    protected $categoryId;

    /**
     * @var null|string[]
     */
    private $localizedNames;

    /**
     * @var null|string[]
     */
    private $localizedLinkRewrites;

    /**
     * @var null|string[]
     */
    private $localizedDescriptions;

    /**
     * @var null|bool
     */
    private $isActive;

    /**
     * @var null|string[]
     */
    private $localizedMetaTitles;

    /**
     * @var null|string[]
     */
    private $localizedMetaDescriptions;

    /**
     * @var null|string[]
     */
    private $localizedMetaKeywords;

    /**
     * @var null|int[]
     */
    private $associatedGroupIds;

    /**
     * @var null|int[]
     */
    private $associatedShopIds;

    /**
     * @param int $categoryId
     */
    public function __construct(int $categoryId)
    {
        $this->categoryId = new CategoryId($categoryId);
    }

    /**
     * @return CategoryId
     */
    public function getCategoryId(): CategoryId
    {
        return $this->categoryId;
    }

    /**
     * @return null|string[]
     */
    public function getLocalizedNames(): ?array
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
    public function setLocalizedNames(array $localizedNames): self
    {
        if (empty($localizedNames)) {
            throw new CategoryConstraintException('Category name cannot be empty', CategoryConstraintException::EMPTY_NAME);
        }

        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getLocalizedLinkRewrites(): ?array
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
    public function setLocalizedLinkRewrites(array $localizedLinkRewrites): self
    {
        if (empty($localizedLinkRewrites)) {
            throw new CategoryConstraintException('Category link rewrite cannot be empty', CategoryConstraintException::EMPTY_LINK_REWRITE);
        }

        $this->localizedLinkRewrites = $localizedLinkRewrites;

        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getLocalizedDescriptions(): ?array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @param string[] $localizedDescriptions
     *
     * @return $this
     */
    public function setLocalizedDescriptions(array $localizedDescriptions): self
    {
        $this->localizedDescriptions = $localizedDescriptions;

        return $this;
    }

    /**
     * @return null|bool
     */
    public function isActive(): ?bool
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
    public function setIsActive(bool $isActive): self
    {
        if (!is_bool($isActive)) {
            throw new CategoryConstraintException('Invalid Category status supplied', CategoryConstraintException::INVALID_STATUS);
        }

        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getLocalizedMetaTitles(): ?array
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @param string[] $localizedMetaTitles
     *
     * @return $this
     */
    public function setLocalizedMetaTitles(array $localizedMetaTitles): self
    {
        $this->localizedMetaTitles = $localizedMetaTitles;

        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getLocalizedMetaDescriptions(): ?array
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @param string[] $localizedMetaDescriptions
     *
     * @return $this
     */
    public function setLocalizedMetaDescriptions(array $localizedMetaDescriptions): self
    {
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;

        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getLocalizedMetaKeywords(): ?array
    {
        return $this->localizedMetaKeywords;
    }

    /**
     * @param string[] $localizedMetaKeywords
     *
     * @return $this
     */
    public function setLocalizedMetaKeywords(array $localizedMetaKeywords): self
    {
        $this->localizedMetaKeywords = $localizedMetaKeywords;

        return $this;
    }

    /**
     * @return null|int[]
     */
    public function getAssociatedGroupIds(): ?array
    {
        return $this->associatedGroupIds;
    }

    /**
     * @param int[] $associatedGroupIds
     *
     * @return $this
     */
    public function setAssociatedGroupIds(array $associatedGroupIds): self
    {
        $this->associatedGroupIds = $associatedGroupIds;

        return $this;
    }

    /**
     * @return null|int[]
     */
    public function getAssociatedShopIds(): ?array
    {
        return $this->associatedShopIds;
    }

    /**
     * @param int[] $associatedShopIds
     *
     * @return $this
     */
    public function setAssociatedShopIds(array $associatedShopIds): self
    {
        $this->associatedShopIds = $associatedShopIds;

        return $this;
    }
}
