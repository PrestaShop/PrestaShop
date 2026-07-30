<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Update\VirtualProductUpdater;
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
     * @var VirtualProductUpdater
     */
    private $virtualProductUpdater;

    /**
     * @param ProductRepository $productRepository
     * @param CombinationRepository $combinationRepository
     * @param DefaultCombinationUpdater $defaultCombinationUpdater
     * @param VirtualProductUpdater $virtualProductUpdater
     */
    public function __construct(
        ProductRepository $productRepository,
        CombinationRepository $combinationRepository,
        DefaultCombinationUpdater $defaultCombinationUpdater,
        VirtualProductUpdater $virtualProductUpdater
    ) {
        $this->productRepository = $productRepository;
        $this->combinationRepository = $combinationRepository;
        $this->defaultCombinationUpdater = $defaultCombinationUpdater;
        $this->virtualProductUpdater = $virtualProductUpdater;
    }

    /**
     * @param CombinationId $combinationId
     * @param ShopConstraint $shopConstraint
     */
    public function deleteCombination(CombinationId $combinationId, ShopConstraint $shopConstraint): void
    {
        $combination = $this->combinationRepository->getByShopConstraint($combinationId, $shopConstraint);
        $productId = new ProductId((int) $combination->id_product);

        $this->virtualProductUpdater->deleteFileForCombination($productId, $combinationId->getValue());
        $this->combinationRepository->delete($combinationId, $shopConstraint);

        if ($combination->default_on) {
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
            foreach ($combinationIds as $combinationId) {
                $this->virtualProductUpdater->deleteFileForCombination($productId, $combinationId->getValue());
            }
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
        if (!ProductType::hasCombinations($product->product_type)) {
            throw new InvalidProductTypeException(InvalidProductTypeException::EXPECTED_COMBINATIONS_TYPE);
        }

        foreach ($this->combinationRepository->getCombinationIds($productId, $shopConstraint) as $combinationId) {
            $this->virtualProductUpdater->deleteFileForCombination($productId, $combinationId->getValue());
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
