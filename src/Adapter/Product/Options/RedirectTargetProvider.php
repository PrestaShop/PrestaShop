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

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryPreviewRepository;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductPreviewRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\RedirectTarget;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;

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
     * @return RedirectTarget|null
     *
     * @throws CategoryNotFoundException
     * @throws ProductNotFoundException
     */
    public function getRedirectTarget(
        string $redirectType,
        int $redirectTargetId
    ): ?RedirectTarget {
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

    /**
     * @param int $redirectTargetId
     *
     * @return RedirectTarget
     *
     * @throws ProductNotFoundException
     */
    private function getProductTarget(int $redirectTargetId): RedirectTarget
    {
        $languageId = $this->legacyContext->getLanguage()->id;
        $productPreview = $this->productPreviewRepository->getPreview(
            new ProductId($redirectTargetId),
            new LanguageId($languageId)
        );

        return new RedirectTarget(
            $redirectTargetId,
            RedirectTarget::PRODUCT_TYPE,
            $productPreview->getName(),
            $productPreview->getImage()
        );
    }

    /**
     * @param int $redirectTargetId
     *
     * @return RedirectTarget
     *
     * @throws CategoryNotFoundException
     */
    private function getCategoryTarget(int $redirectTargetId): RedirectTarget
    {
        $languageId = (int) $this->legacyContext->getLanguage()->id;
        $category = $this->categoryPreviewRepository->getPreview(
            new CategoryId($redirectTargetId),
            new LanguageId($languageId)
        );

        return new RedirectTarget(
            $redirectTargetId,
            RedirectTarget::CATEGORY_TYPE,
            $category->getBreadcrumb(),
            $category->getImage()
        );
    }
}
