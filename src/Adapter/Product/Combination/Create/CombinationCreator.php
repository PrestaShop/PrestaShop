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
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
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
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CombinationMultiShopRepository
     */
    private $combinationMultiShopRepository;

    /**
     * @param CombinationGeneratorInterface $combinationGenerator
     * @param CombinationRepository $combinationRepository
     * @param CombinationMultiShopRepository $combinationMultiShopRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        CombinationGeneratorInterface $combinationGenerator,
        CombinationRepository $combinationRepository,
        CombinationMultiShopRepository $combinationMultiShopRepository,
        ProductRepository $productRepository
    ) {
        $this->combinationGenerator = $combinationGenerator;
        $this->combinationRepository = $combinationRepository;
        $this->combinationMultiShopRepository = $combinationMultiShopRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param ProductId $productId
     * @param GroupedAttributeIds[] $groupedAttributeIdsList
     * @param ShopId $shopId
     *
     * @return CombinationId[]
     *
     * @throws CoreException
     * @throws InvalidProductTypeException
     */
    public function createCombinations(ProductId $productId, array $groupedAttributeIdsList, ShopId $shopId): array
    {
        $product = $this->productRepository->get($productId);
        if ($product->product_type !== ProductType::TYPE_COMBINATIONS) {
            throw new InvalidProductTypeException(InvalidProductTypeException::EXPECTED_COMBINATIONS_TYPE);
        }

        $generatedCombinations = $this->combinationGenerator->generate($this->formatScalarValues($groupedAttributeIdsList));

        // avoid applying specificPrice on each combination.
        $this->disableSpecificPriceRulesApplication();

        $combinationIds = $this->addCombinations($product, $generatedCombinations, $shopId);

        // apply all specific price rules at once after all the combinations are generated
        $this->applySpecificPriceRules($productId);

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
     * @param ShopId $shopId
     *
     * @return CombinationId[]
     */
    private function addCombinations(Product $product, Traversable $generatedCombinations, ShopId $shopId): array
    {
        $product->setAvailableDate();
        $productId = new ProductId((int) $product->id);
        $alreadyHasCombinations = $hasDefault = $this->combinationRepository->findDefaultCombination($productId);
        $addedCombinationIds = [];
        foreach ($generatedCombinations as $generatedCombination) {
            $associatedWithAttributes = false;
            // Product already has combinations so we need to filter existing ones
            if ($alreadyHasCombinations) {
                $attributeIds = array_values($generatedCombination);
                $matchingCombinationId = $this->combinationRepository->findCombinationIdByAttributes($productId, $attributeIds);
                $associatedWithShop = $this->combinationMultiShopRepository->isAssociatedWithShop($matchingCombinationId, $shopId);
                if ($matchingCombinationId && $associatedWithShop) {
                    continue;
                }

                if ($matchingCombinationId && !$associatedWithShop) {
                    $this->combinationMultiShopRepository->addToShop($matchingCombinationId, $shopId);
                    continue;
                }
            }

            $addedCombinationIds[] = $this->persistCombination($productId, $generatedCombination, !$hasDefault, $shopId);
            $hasDefault = true;
        }

        return $addedCombinationIds;
    }

    /**
     * @param ProductId $productId
     * @param int[] $generatedCombination
     * @param bool $isDefault
     * @param ShopId $shopId
     *
     * @return CombinationId
     */
    private function persistCombination(
        ProductId $productId,
        array $generatedCombination,
        bool $isDefault,
        ShopId $shopId
    ): CombinationId {
        $combination = $this->combinationMultiShopRepository->create($productId, $isDefault, $shopId);
        $combinationId = new CombinationId((int) $combination->id);

        //@todo: Use DB transaction instead if they are accepted (PR #21740)
        try {
            $this->combinationRepository->saveProductAttributeAssociation($combinationId, $generatedCombination);
        } catch (CoreException $e) {
            $this->combinationMultiShopRepository->delete($combinationId, ShopConstraint::shop($shopId->getValue()));
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
