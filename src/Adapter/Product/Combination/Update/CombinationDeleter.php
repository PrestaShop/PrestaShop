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

use Combination;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotDeleteCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Product;

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
     * @var CombinationMultiShopRepository
     */
    private $combinationMultiShopRepository;

    /**
     * @var DefaultCombinationUpdater
     */
    private $defaultCombinationUpdater;

    /**
     * @param ProductRepository $productRepository
     * @param CombinationRepository $combinationRepository
     * @param CombinationMultiShopRepository $combinationMultiShopRepository
     * @param DefaultCombinationUpdater $defaultCombinationUpdater
     */
    public function __construct(
        ProductRepository $productRepository,
        CombinationRepository $combinationRepository,
        CombinationMultiShopRepository $combinationMultiShopRepository,
        DefaultCombinationUpdater $defaultCombinationUpdater
    ) {
        $this->productRepository = $productRepository;
        $this->combinationMultiShopRepository = $combinationMultiShopRepository;
        $this->defaultCombinationUpdater = $defaultCombinationUpdater;
        $this->combinationRepository = $combinationRepository;
    }

    /**
     * @param CombinationId $combinationId
     * @param ShopConstraint $shopConstraint
     */
    public function deleteCombination(CombinationId $combinationId, ShopConstraint $shopConstraint): void
    {
        $combination = $this->combinationMultiShopRepository->getByShopConstraint($combinationId, $shopConstraint);
        $this->combinationMultiShopRepository->delete($combinationId, $shopConstraint);

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
            $this->combinationMultiShopRepository->bulkDelete($combinationIds, $shopConstraint);
        } finally {
            $this->updateDefaultCombination($productId, $shopConstraint);
        }
    }

    /**
     * @todo: missing shop constraint
     *
     * @param ProductId $productId
     *
     * @throws InvalidProductTypeException
     * @throws CannotDeleteCombinationException
     * @throws CoreException
     */
    public function deleteAllProductCombinations(ProductId $productId): void
    {
        $product = $this->productRepository->get($productId);
        if ($product->product_type !== ProductType::TYPE_COMBINATIONS) {
            throw new InvalidProductTypeException(InvalidProductTypeException::EXPECTED_COMBINATIONS_TYPE);
        }

        $this->combinationRepository->deleteByProductId($productId);
    }

    /**
     * @todo: work in progress.
     *
     * @param ProductId $productId
     */
    private function updateDefaultCombination(ProductId $productId, ShopConstraint $shopConstraint): void
    {
        // we already assume it is the default combination that was deleted (by the if stmt above)

        $defaultShopId = $this->combinationMultiShopRepository->getProductDefaultShopId($productId);
        $newDefaultCombinationId = $this->combinationMultiShopRepository->findFirstCombinationId($productId, $shopConstraint);
        $newDefaultCombination = null;

        if ($newDefaultCombinationId) {
            $newDefaultCombination = $this->combinationMultiShopRepository->getByShopConstraint($newDefaultCombinationId, $shopConstraint);
        }

        // check if it is the default shop for product
        if ($shopConstraint->forAllShops() || ($defaultShopId->getValue() === $shopConstraint->getShopId()->getValue())) {

            // this means we have deleted default combination for default shop, so it must be synced with other tables
            // 1. find next default combination for default shop
            // 2. update product_attribute.default_on
            // @todo: 3. update product.cache_default_attribute
            if ($newDefaultCombination) {
                // update combination.default_on for product_attribute
                // @todo: this causes duplicate entry in product_attribute.default_on
                $this->updateCombinationDefaultProperty($newDefaultCombination, true, null);
            }
            // update product.cache_default_attribute
            // @todo: this probably causes freeze in terminal
            Product::updateDefaultAttribute($productId->getValue());
        }

        if ($newDefaultCombination) {
            // update combination.default_on for product_attribute_shop
            $this->updateCombinationDefaultProperty($newDefaultCombination, true, $shopConstraint);
        }
    }


    /**
     * @param Combination $combination
     * @param bool $isDefault
     * @param ShopConstraint|null $shopConstraint
     */
    private function updateCombinationDefaultProperty(Combination $combination, bool $isDefault, ?ShopConstraint $shopConstraint): void
    {
        $combination->default_on = $isDefault;

        if ($shopConstraint) {
            $this->combinationMultiShopRepository->partialUpdate(
                $combination,
                ['default_on'],
                $shopConstraint,
                CannotUpdateCombinationException::FAILED_UPDATE_DEFAULT_COMBINATION
            );
        } else {
            $this->combinationRepository->partialUpdate(
                $combination,
                ['default_on'],
                CannotUpdateCombinationException::FAILED_UPDATE_DEFAULT_COMBINATION
            );
        }
    }
}
