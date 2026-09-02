<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

/**
 * Responsible for updating product default combination
 */
class DefaultCombinationUpdater
{
    /**
     * @param CombinationRepository $combinationRepository
     * @param ProductRepository $productRepository
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(
        private CombinationRepository $combinationRepository,
        private ProductRepository $productRepository,
        private HookDispatcherInterface $hookDispatcher
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Marks the provided combination as default (combination->default_on)
     * and removes the mark from previous default combination
     *
     * Notice: Product->cache_default_attribute is updated in Product add(), update(), delete() methods.
     *
     * @see Product::updateDefaultAttribute()
     *
     * @param CombinationId $defaultCombinationId
     * @param ShopConstraint $shopConstraint
     */
    public function setDefaultCombination(CombinationId $defaultCombinationId, ShopConstraint $shopConstraint): void
    {
        $newDefaultCombination = $this->combinationRepository->getByShopConstraint($defaultCombinationId, $shopConstraint);
        $productId = new ProductId((int) $newDefaultCombination->id_product);

        $this->combinationRepository->setDefaultCombination(
            $productId,
            $defaultCombinationId,
            $shopConstraint
        );

        $this->productRepository->updateCachedDefaultCombination($productId, $shopConstraint);

        $this->hookDispatcher->dispatchWithParameters(
            'actionUpdateDefaultCombinationAfter',
            [
                'id_product' => (int) $newDefaultCombination->id_product,
                'id_product_attribute' => (int) $defaultCombinationId->getValue(),
                'id_shop' => $shopConstraint->isSingleShopContext() ? $shopConstraint->getShopId()->getValue() : null,
            ]
        );
    }
}
