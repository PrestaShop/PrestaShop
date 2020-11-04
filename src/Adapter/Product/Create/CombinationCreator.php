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

namespace PrestaShop\PrestaShop\Adapter\Product\Create;

use Combination;
use PrestaShop\PrestaShop\Adapter\Product\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Product\Generator\CombinationGeneratorInterface;
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
     * @param CombinationGeneratorInterface $combinationGenerator
     * @param CombinationRepository $combinationRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        CombinationGeneratorInterface $combinationGenerator,
        CombinationRepository $combinationRepository,
        ProductRepository $productRepository
    ) {
        $this->combinationGenerator = $combinationGenerator;
        $this->combinationRepository = $combinationRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param ProductId $productId
     * @param array<int, array<int, int>> $groupedAttributeIds
     *
     * @todo: multistore
     *
     * @return CombinationId[]
     */
    public function createCombinations(ProductId $productId, array $groupedAttributeIds): array
    {
        $product = $this->productRepository->get($productId);
        $generatedCombinations = $this->combinationGenerator->generate($groupedAttributeIds);

        // avoid applying specificPrice on each combination.
        SpecificPriceRule::disableAnyApplication();

        $combinationIds = $this->addCombinations($product, $generatedCombinations);

        Product::updateDefaultAttribute($product->id);
        SpecificPriceRule::enableAnyApplication();
        // apply all specific price rules at once after all the combinations are generated
        SpecificPriceRule::applyAllRules([$product->id]);

        return $combinationIds;
    }

    /**
     * @param Product $product
     * @param Traversable $generatedCombinations
     *
     * @return CombinationId[]
     *
     * @throws CombinationException
     */
    private function addCombinations(Product $product, Traversable $generatedCombinations): array
    {
        $product->setAvailableDate();

        $addedCombinationIds = [];
        foreach ($generatedCombinations as $generatedCombination) {
            $combinationExists = $product->productAttributeExists($generatedCombination, false, null, true);

            if ($combinationExists) {
                continue;
            }

            $addedCombinationIds[] = $this->addCombination((int) $product->id, $generatedCombination);
        }

        return $addedCombinationIds;
    }

    /**
     * @param int $productId
     * @param int[] $generatedCombination
     *
     * @return CombinationId
     */
    private function addCombination(int $productId, array $generatedCombination): CombinationId
    {
        $newCombination = new Combination();
        $newCombination->id_product = $productId;
        $newCombination->default_on = 0;

        $combinationId = $this->combinationRepository->add($newCombination);
        $this->combinationRepository->saveProductAttributeAssociation($combinationId, $generatedCombination);

        return $combinationId;
    }
}
