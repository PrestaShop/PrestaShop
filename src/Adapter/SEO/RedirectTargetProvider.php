<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SEO;

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryPreviewRepository;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductPreviewRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\RedirectType as CategoryRedirectType;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType as ProductRedirectType;
use PrestaShop\PrestaShop\Core\Domain\QueryResult\RedirectTargetInformation;

/**
 * Build details on the product target based on the configuration (redirection type and entity id)
 */
class RedirectTargetProvider
{
    /**
     * @var ProductPreviewRepository
     */
    private $productPreviewRepository;

    /**
     * @var CategoryPreviewRepository
     */
    private $categoryPreviewRepository;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @param ProductPreviewRepository $productPreviewRepository
     * @param CategoryPreviewRepository $categoryPreviewRepository
     * @param LegacyContext $legacyContext
     */
    public function __construct(
        ProductPreviewRepository $productPreviewRepository,
        CategoryPreviewRepository $categoryPreviewRepository,
        LegacyContext $legacyContext
    ) {
        $this->productPreviewRepository = $productPreviewRepository;
        $this->categoryPreviewRepository = $categoryPreviewRepository;
        $this->legacyContext = $legacyContext;
    }

    /**
     * @param string $redirectType
     * @param int $redirectTargetId
     *
     * @return RedirectTargetInformation|null
     *
     * @throws CategoryNotFoundException
     * @throws ProductNotFoundException
     */
    public function getRedirectTarget(
        string $redirectType,
        int $redirectTargetId
    ): ?RedirectTargetInformation {
        if (empty($redirectTargetId)) {
            return null;
        }

        switch ($redirectType) {
            case ProductRedirectType::TYPE_PRODUCT_TEMPORARY:
            case ProductRedirectType::TYPE_PRODUCT_PERMANENT:
                return $this->getProductTarget($redirectTargetId);
            case ProductRedirectType::TYPE_CATEGORY_TEMPORARY:
            case ProductRedirectType::TYPE_CATEGORY_PERMANENT:
            case CategoryRedirectType::TYPE_TEMPORARY:
            case CategoryRedirectType::TYPE_PERMANENT:
                return $this->getCategoryTarget($redirectTargetId);
            default:
                return null;
        }
    }

    /**
     * @param int $redirectTargetId
     *
     * @return RedirectTargetInformation
     *
     * @throws ProductNotFoundException
     */
    private function getProductTarget(int $redirectTargetId): RedirectTargetInformation
    {
        $languageId = $this->legacyContext->getLanguage()->id;
        $productPreview = $this->productPreviewRepository->getPreview(
            new ProductId($redirectTargetId),
            new LanguageId($languageId)
        );

        return new RedirectTargetInformation(
            $redirectTargetId,
            RedirectTargetInformation::PRODUCT_TYPE,
            $productPreview->getName(),
            $productPreview->getImage()
        );
    }

    /**
     * @param int $redirectTargetId
     *
     * @return RedirectTargetInformation
     *
     * @throws CategoryNotFoundException
     */
    private function getCategoryTarget(int $redirectTargetId): RedirectTargetInformation
    {
        $languageId = (int) $this->legacyContext->getLanguage()->id;
        $category = $this->categoryPreviewRepository->getPreview(
            new CategoryId($redirectTargetId),
            new LanguageId($languageId)
        );

        return new RedirectTargetInformation(
            $redirectTargetId,
            RedirectTargetInformation::CATEGORY_TYPE,
            $category->getBreadcrumb(),
            $category->getImage()
        );
    }
}
