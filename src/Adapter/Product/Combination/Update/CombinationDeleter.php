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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotDeleteCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

class CombinationDeleter
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var DefaultCombinationUpdater
     */
    private $defaultCombinationUpdater;

    /**
     * @param ProductRepository $productRepository
     * @param CombinationRepository $combinationRepository
     * @param DefaultCombinationUpdater $defaultCombinationUpdater
     */
    public function __construct(
        ProductRepository $productRepository,
        CombinationRepository $combinationRepository,
        DefaultCombinationUpdater $defaultCombinationUpdater
    ) {
        $this->productRepository = $productRepository;
        $this->combinationRepository = $combinationRepository;
        $this->defaultCombinationUpdater = $defaultCombinationUpdater;
    }

    /**
     * @param CombinationId $combinationId
     * @param ShopConstraint $shopConstraint
     */
    public function deleteCombination(CombinationId $combinationId, ShopConstraint $shopConstraint): void
    {
        $combination = $this->combinationRepository->getByShopConstraint($combinationId, $shopConstraint);
        $this->combinationRepository->delete($combinationId, $shopConstraint);

        if ($combination->default_on) {
            $productId = new ProductId((int) $combination->id_product);
            $this->updateDefaultCombination($productId, $shopConstraint);
        }
    }

    /**
     * @param ProductId $productId
     * @param CombinationId[] $combinationIds
     */
    public function bulkDeleteProductCombinations(ProductId $productId, array $combinationIds, ShopConstraint $shopConstraint): void
    {
        try {
            $this->combinationRepository->bulkDelete($combinationIds, $shopConstraint);
        } finally {
            $this->updateDefaultCombination($productId, $shopConstraint);
        }
    }

    /**
     * @param ProductId $productId
     *
     * @throws InvalidProductTypeException
     * @throws CannotDeleteCombinationException
     * @throws CoreException
     */
    public function deleteAllProductCombinations(ProductId $productId, ShopConstraint $shopConstraint): void
    {
        $product = $this->productRepository->getByShopConstraint($productId, $shopConstraint);
        if ($product->product_type !== ProductType::TYPE_COMBINATIONS) {
            throw new InvalidProductTypeException(InvalidProductTypeException::EXPECTED_COMBINATIONS_TYPE);
        }

        $this->combinationRepository->deleteByProductId($productId, $shopConstraint);
    }

    /**
     * @param ProductId $productId
     */
    private function updateDefaultCombination(ProductId $productId, ShopConstraint $shopConstraint): void
    {
        $newDefaultCombinationId = $this->combinationRepository->findFirstCombinationId($productId, $shopConstraint);

        if (!$newDefaultCombinationId) {
            $this->productRepository->updateCachedDefaultCombination($productId, $shopConstraint);

            return;
        }

        $this->defaultCombinationUpdater->setDefaultCombination($newDefaultCombinationId, $shopConstraint);
    }
}
