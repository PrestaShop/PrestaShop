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

namespace PrestaShop\PrestaShop\Adapter\Product\Options;

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Image\ImagePathFactory;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductPreviewRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductRedirectTarget;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;

class RedirectTargetProvider
{
    /**
     * @var ProductPreviewRepository
     */
    private $productPreviewRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var ImagePathFactory
     */
    private $categoryImagePathFactory;

    /**
     * @param ProductPreviewRepository $productPreviewRepository
     * @param CategoryRepository $categoryRepository
     * @param LegacyContext $legacyContext
     * @param ImagePathFactory $categoryImagePathFactory
     */
    public function __construct(
        ProductPreviewRepository $productPreviewRepository,
        CategoryRepository $categoryRepository,
        LegacyContext $legacyContext,
        ImagePathFactory $categoryImagePathFactory
    ) {
        $this->productPreviewRepository = $productPreviewRepository;
        $this->categoryRepository = $categoryRepository;
        $this->legacyContext = $legacyContext;
        $this->categoryImagePathFactory = $categoryImagePathFactory;
    }

    /**
     * @param string $redirectType
     * @param int $redirectTargetId
     *
     * @return ProductRedirectTarget|null
     */
    public function getRedirectTarget(
        string $redirectType,
        int $redirectTargetId
    ): ?ProductRedirectTarget {
        if (empty($redirectTargetId)) {
            return null;
        }

        switch ($redirectType) {
            case RedirectType::TYPE_PRODUCT_TEMPORARY:
            case RedirectType::TYPE_PRODUCT_PERMANENT:
                return $this->getProductTarget($redirectTargetId);
            case RedirectType::TYPE_CATEGORY_TEMPORARY:
            case RedirectType::TYPE_CATEGORY_PERMANENT:
                return $this->getCategoryTarget($redirectTargetId);
            default:
                return null;
        }
    }

    private function getProductTarget(int $redirectTargetId): ProductRedirectTarget
    {
        $languageId = (int) $this->legacyContext->getLanguage()->id;
        $product = $this->productPreviewRepository->getPreview(
            new ProductId($redirectTargetId),
            new LanguageId($languageId)
        );

        return new ProductRedirectTarget(
            $redirectTargetId,
            ProductRedirectTarget::PRODUCT_TYPE,
            $product->getName(),
            $product->getImage()
        );
    }

    private function getCategoryTarget(int $redirectTargetId): ProductRedirectTarget
    {
        $languageId = (int) $this->legacyContext->getLanguage()->id;

        return new ProductRedirectTarget(
            $redirectTargetId,
            ProductRedirectTarget::CATEGORY_TYPE,
            $this->categoryRepository->getBreadcrumb(
                new CategoryId($redirectTargetId),
                new LanguageId($languageId)
            ),
            $this->categoryImagePathFactory->getPath($redirectTargetId)
        );
    }
}
