<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Category\Repository;

use Language;
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

        if (file_exists(_PS_CAT_IMG_DIR_ . $categoryId->getValue() . '.jpg')) {
            $imagePath = $this->categoryImagePathFactory->getPath($categoryId->getValue());
        } else {
            $imagePath = $this->categoryImagePathFactory->getPath(Language::getIsoById($languageId->getValue()) . '-default-category_default');
        }

        return new CategoryPreview(
            $categoryId->getValue(),
            $name,
            $breadcrumb,
            $imagePath
        );
    }
}
