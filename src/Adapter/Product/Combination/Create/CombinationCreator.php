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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Create;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\DefaultCombinationUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\GroupedAttributeIds;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Product\Combination\Generator\CombinationGeneratorInterface;
use PrestaShopException;
use Product;
use SpecificPriceRule;
use Traversable;

/**
 * Creates combinations from attributes
 */
class CombinationCreator
{
    /**
     * @var CombinationGeneratorInterface
     */
    private $combinationGenerator;

    /**
     * @var ProductMultiShopRepository
     */
    private $productRepository;

    /**
     * @var CombinationMultiShopRepository
     */
    private $combinationRepository;

    /**
     * @var StockAvailableMultiShopRepository
     */
    private $stockAvailableMultiShopRepository;

    /**
     * @var DefaultCombinationUpdater
     */
    private $defaultCombinationUpdater;

    /**
     * @param CombinationGeneratorInterface $combinationGenerator
     * @param CombinationMultiShopRepository $combinationRepository
     * @param ProductMultiShopRepository $productRepository
     * @param StockAvailableMultiShopRepository $stockAvailableMultiShopRepository
     * @param DefaultCombinationUpdater $defaultCombinationUpdater
     */
    public function __construct(
        CombinationGeneratorInterface $combinationGenerator,
        CombinationMultiShopRepository $combinationRepository,
        ProductMultiShopRepository $productRepository,
        StockAvailableMultiShopRepository $stockAvailableMultiShopRepository,
        DefaultCombinationUpdater $defaultCombinationUpdater
    ) {
        $this->combinationGenerator = $combinationGenerator;
        $this->combinationRepository = $combinationRepository;
        $this->productRepository = $productRepository;
        $this->stockAvailableMultiShopRepository = $stockAvailableMultiShopRepository;
        $this->defaultCombinationUpdater = $defaultCombinationUpdater;
    }

    /**
     * @param ProductId $productId
     * @param GroupedAttributeIds[] $groupedAttributeIdsList
     * @param ShopConstraint $shopConstraint
     *
     * @return CombinationId[]
     *
     * @throws CoreException
     * @throws InvalidProductTypeException
     */
    public function createCombinations(ProductId $productId, array $groupedAttributeIdsList, ShopConstraint $shopConstraint): array
    {
        $product = $this->productRepository->getByShopConstraint($productId, $shopConstraint);
        if ($product->product_type !== ProductType::TYPE_COMBINATIONS) {
            throw new InvalidProductTypeException(InvalidProductTypeException::EXPECTED_COMBINATIONS_TYPE);
        }

        $generatedCombinations = $this->combinationGenerator->generate($this->formatScalarValues($groupedAttributeIdsList));

        // avoid applying specificPrice on each combination.
        $this->disableSpecificPriceRulesApplication();

        $combinationIds = $this->addCombinations(
            $product,
            $generatedCombinations,
            $this->productRepository->getShopIdsByConstraint($productId, $shopConstraint)
        );

        // apply all specific price rules at once after all the combinations are generated
        $this->applySpecificPriceRules($productId);
        $this->productRepository->updateCachedDefaultCombination($productId, $shopConstraint);

        return $combinationIds;
    }

    /**
     * @param GroupedAttributeIds[] $groupedAttributeIdsList
     *
     * @return array<int, array<int, int>>
     */
    private function formatScalarValues(array $groupedAttributeIdsList): array
    {
        $groupedIdsList = [];
        foreach ($groupedAttributeIdsList as $groupedAttributeIds) {
            foreach ($groupedAttributeIds->getAttributeIds() as $attributeId) {
                $groupedIdsList[$groupedAttributeIds->getAttributeGroupId()->getValue()][] = $attributeId->getValue();
            }
        }

        return $groupedIdsList;
    }

    /**
     * @param Product $product
     * @param Traversable $generatedCombinations
     * @param ShopId[] $shopIds
     *
     * @return CombinationId[] the ids of newly created combinations
     */
    private function addCombinations(Product $product, Traversable $generatedCombinations, array $shopIds): array
    {
        $product->setAvailableDate();
        $productId = new ProductId((int) $product->id);
        $hasCombinations = $this->productRepository->hasCombinations($productId);
        $newCombinationIds = [];

        foreach ($shopIds as $shopId) {
            foreach ($generatedCombinations as $generatedCombination) {
                if (!$hasCombinations) {
                    // Product has no combinations yet, so we create new combinations and skip the rest of the loop
                    $newCombinationIds[] = $this->persistCombination($productId, $generatedCombination, $shopId);
                    continue;
                }

                $attributeIds = array_values($generatedCombination);
                $matchingCombinationId = $this->combinationRepository->findCombinationIdByAttributes($productId, $attributeIds);

                if (!$matchingCombinationId) {
                    // if there is no combination of provided attributes yet, we create a new one and skip to next iteration
                    $newCombinationIds[] = $this->persistCombination($productId, $generatedCombination, $shopId);
                    continue;
                }

                // if there is a combination of provided attributes, and it is already associated with the shop, then we don't do anything and skip to next iteration
                if ($this->combinationRepository->isAssociatedWithShop($matchingCombinationId, $shopId)) {
                    continue;
                }

                // when combination already exists in product_attribute, then we add it to related product_attribute_shop
                $this->combinationRepository->addToShop($matchingCombinationId, $shopId);
                // create dedicated stock_available for combination in related shop
                $this->stockAvailableMultiShopRepository->createStockAvailable($productId, $shopId, $matchingCombinationId);
            }

            // set default combination if none is set yet
            if (!$this->combinationRepository->findDefaultCombinationIdForShop($productId, $shopId)) {
                $shopConstraint = ShopConstraint::shop($shopId->getValue());
                $firstCombinationId = $this->combinationRepository->findFirstCombinationId($productId, $shopConstraint);
                $this->defaultCombinationUpdater->setDefaultCombination($firstCombinationId, $shopConstraint);
            }
        }

        return $newCombinationIds;
    }

    /**
     * @param ProductId $productId
     * @param int[] $generatedCombination
     * @param ShopId $shopId
     *
     * @return CombinationId
     */
    private function persistCombination(
        ProductId $productId,
        array $generatedCombination,
        ShopId $shopId
    ): CombinationId {
        $combination = $this->combinationRepository->create($productId, $shopId);
        $combinationId = new CombinationId((int) $combination->id);

        //@todo: Use DB transaction instead if they are accepted (PR #21740)
        try {
            $this->combinationRepository->saveProductAttributeAssociation($combinationId, $generatedCombination);
        } catch (CoreException $e) {
            $this->combinationRepository->delete($combinationId, ShopConstraint::shop($shopId->getValue()));
            throw $e;
        }

        return $combinationId;
    }

    /**
     * @throws CoreException
     */
    private function disableSpecificPriceRulesApplication(): void
    {
        try {
            SpecificPriceRule::disableAnyApplication();
        } catch (PrestaShopException $e) {
            throw new CoreException('Error occurred when trying to disable specific price rules application', 0, $e);
        }
    }

    /**
     * @param ProductId $productId
     *
     * @throws CoreException
     */
    private function applySpecificPriceRules(ProductId $productId): void
    {
        try {
            SpecificPriceRule::enableAnyApplication();
            SpecificPriceRule::applyAllRules([$productId->getValue()]);
        } catch (PrestaShopException $e) {
            throw new CoreException('Error occurred when trying to apply specific prices rules', 0, $e);
        }
    }
}
