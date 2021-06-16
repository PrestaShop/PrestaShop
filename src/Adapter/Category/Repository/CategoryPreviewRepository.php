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

namespace PrestaShop\PrestaShop\Adapter\Category\Repository;

use PrestaShop\PrestaShop\Adapter\Image\ImagePathFactory;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\CategoryPreview;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Returns preview data for a category or a list of product
 *
 * @todo add function for the list that should be used in the new category search API
 */
class CategoryPreviewRepository
{
    public const BREADCRUMB_SEPARATOR = ' > ';

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var ImagePathFactory
     */
    private $categoryImagePathFactory;

    /**
     * @param CategoryRepository $categoryRepository
     * @param ImagePathFactory $categoryImagePathFactory
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ImagePathFactory $categoryImagePathFactory
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryImagePathFactory = $categoryImagePathFactory;
    }

    /**
     * @param CategoryId $categoryId
     * @param LanguageId $languageId
     *
     * @return CategoryPreview
     *
     * @throws CategoryNotFoundException
     */
    public function getPreview(CategoryId $categoryId, LanguageId $languageId): CategoryPreview
    {
        $breadcrumb = $this->categoryRepository->getBreadcrumb(
            $categoryId,
            $languageId,
            static::BREADCRUMB_SEPARATOR
        );
        $names = explode(static::BREADCRUMB_SEPARATOR, $breadcrumb);
        $name = $names[count($names) - 1] ?? $names[0];
        $imagePath = $this->categoryImagePathFactory->getPath($categoryId->getValue());

        return new CategoryPreview(
            $categoryId->getValue(),
            $name,
            $breadcrumb,
            $imagePath
        );
    }
}
