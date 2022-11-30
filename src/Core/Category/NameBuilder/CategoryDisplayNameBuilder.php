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

namespace PrestaShop\PrestaShop\Core\Category\NameBuilder;

use Cache;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Builds category display name if it needs to differ from original category name.
 */
class CategoryDisplayNameBuilder
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var string
     */
    private $breadcrumbSeparator;

    /**
     * @param CategoryRepository $categoryRepository
     * @param string $breadcrumbSeparator
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        string $breadcrumbSeparator
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->breadcrumbSeparator = $breadcrumbSeparator;
    }

    /**
     * If there are multiple categories with identical names, we want to be able to tell them apart,
     * so we build a display name which may include some (or all) parent category names.
     *
     * @see buildBreadcrumb for more details
     *
     * @param string $categoryName
     * @param ShopId $shopId
     * @param LanguageId $languageId
     * @param CategoryId $categoryId
     * @param bool $useCache
     *
     * @return string
     */
    public function build(
        string $categoryName,
        ShopId $shopId,
        LanguageId $languageId,
        CategoryId $categoryId,
        bool $useCache = true
    ): string {
        $duplicateNameIds = $this->getDuplicateNameIds($shopId, $languageId, $useCache);
        $isDuplicate = false;

        foreach ($duplicateNameIds as $id) {
            if ($categoryId->getValue() === $id->getValue()) {
                $isDuplicate = true;
            }
        }

        if (!$isDuplicate) {
            return $categoryName;
        }

        return $this->buildBreadcrumb($categoryId, $this->getDuplicateCategoriesBreadcrumbs($duplicateNameIds, $shopId, $languageId, $useCache));
    }

    /**
     * @param ShopId $shopId
     * @param LanguageId $languageId
     * @param bool $useCache
     *
     * @return CategoryId[]
     */
    private function getDuplicateNameIds(ShopId $shopId, LanguageId $languageId, bool $useCache): array
    {
        if (!$useCache) {
            return $this->categoryRepository->getDuplicateNameIds($shopId, $languageId);
        }

        $cacheKey = $this->buildCacheKeyForNameIds($shopId, $languageId);

//      @todo: consider using Symfony\Component\Cache\Adapter\AdapterInterface instead of legacy Cache
        if (Cache::isStored($this->buildCacheKeyForNameIds($shopId, $languageId))) {
            return Cache::retrieve($cacheKey);
        }

        $duplicateNameIds = $this->categoryRepository->getDuplicateNameIds($shopId, $languageId);
        Cache::store($cacheKey, $duplicateNameIds);

        return $duplicateNameIds;
    }

    /**
     * @param CategoryId[] $categoryIds
     * @param ShopId $shopId
     * @param LanguageId $languageId
     * @param bool $useCache
     *
     * @return array<int, string[]>
     */
    private function getDuplicateCategoriesBreadcrumbs(array $categoryIds, ShopId $shopId, LanguageId $languageId, bool $useCache): array
    {
        if (!$useCache) {
            return $this->fetchBreadcrumbs($categoryIds, $languageId);
        }
        $cacheKey = $this->buildCacheKeyForBreadcrumbs($shopId, $languageId);

//      @todo: consider using Symfony\Component\Cache\Adapter\AdapterInterface instead of legacy Cache
        if (Cache::isStored($this->buildCacheKeyForBreadcrumbs($shopId, $languageId))) {
            return Cache::retrieve($cacheKey);
        }

        $duplicateCategoriesBreadcrumbs = $this->fetchBreadcrumbs($categoryIds, $languageId);
        Cache::store($cacheKey, $duplicateCategoriesBreadcrumbs);

        return $duplicateCategoriesBreadcrumbs;
    }

    /**
     * @param CategoryId[] $categoryIds
     * @param LanguageId $languageId
     *
     * @return array<int, string[]>
     */
    private function fetchBreadcrumbs(array $categoryIds, LanguageId $languageId): array
    {
        $duplicateCategoriesBreadcrumbs = [];
        foreach ($categoryIds as $categoryId) {
            $duplicateCategoriesBreadcrumbs[$categoryId->getValue()] = $this->categoryRepository->getBreadcrumbParts(
                $categoryId,
                $languageId
            );
        }

        return $duplicateCategoriesBreadcrumbs;
    }

    /**
     * Builds a breadcrumb which consists of minimum needed parents to be unique, depending on other categories breadcrumbs.
     * If breadcrumb is still not unique after showing all of it, then we append category id.
     *
     * E.g.:
     *      For categories: Home > Clothes, Home > Clothes
     *          breadcrumbs would be: Home > Clothes (#id1), Home > Clothes (#id2)
     *
     *      For: Home > Clothes, Home > Clothes > Clothes:
     *          breadcrumbs would be: Home > Clothes, Clothes > Clothes
     *
     * @param CategoryId $categoryId
     * @param array<int, string[]> $duplicateCategoriesBreadcrumbs
     *
     * @return string
     */
    private function buildBreadcrumb(CategoryId $categoryId, array $duplicateCategoriesBreadcrumbs): string
    {
        $breadcrumbParts = $duplicateCategoriesBreadcrumbs[$categoryId->getValue()];
        unset($duplicateCategoriesBreadcrumbs[$categoryId->getValue()]);

        $maxSteps = count($breadcrumbParts);
        $breadcrumb = $breadcrumbParts[0];
        $duplicatedFound = true;

        if ($maxSteps > 1) {
            $duplicatedFound = false;

            for ($step = 2; $step <= $maxSteps; ++$step) {
                $breadcrumb = $this->extractBreadcrumbFromParts($breadcrumbParts, $step);
                $duplicatedFound = false;
                foreach ($duplicateCategoriesBreadcrumbs as $otherCategoryBreadcrumbs) {
                    $otherBreadcrumb = $this->extractBreadcrumbFromParts($otherCategoryBreadcrumbs, $step);
                    if ($otherBreadcrumb === $breadcrumb) {
                        $duplicatedFound = true;
                        break;
                    }
                }

                if (!$duplicatedFound) {
                    break;
                }
            }
        }

        if ($duplicatedFound) {
            // if breadcrumbs are still duplicated append category id
            $breadcrumb = sprintf('%s (#%d)', $breadcrumb, $categoryId->getValue());
        }

        return $breadcrumb;
    }

    /**
     * @param string[] $parts
     * @param int $step
     *
     * @return string
     */
    private function extractBreadcrumbFromParts(array $parts, int $step): string
    {
        return implode($this->breadcrumbSeparator, array_slice($parts, -$step, $step));
    }

    /**
     * @param ShopId $shopId
     * @param LanguageId $langId
     *
     * @return string
     */
    private function buildCacheKeyForNameIds(ShopId $shopId, LanguageId $langId): string
    {
        return sprintf(
            'Category::duplicateCategoryNameIds_shop_%s_lang_%s',
            $shopId->getValue(),
            $langId->getValue()
        );
    }

    /**
     * @param ShopId $shopId
     * @param LanguageId $langId
     *
     * @return string
     */
    private function buildCacheKeyForBreadcrumbs(ShopId $shopId, LanguageId $langId): string
    {
        return sprintf(
            'Category::duplicateCategoryBreadcrumbs_shop_%s_lang_%s',
            $shopId->getValue(),
            $langId->getValue()
        );
    }
}
