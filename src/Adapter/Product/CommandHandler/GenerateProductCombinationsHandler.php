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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use Combination;
use Db;
use Iterator;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\GenerateProductCombinationsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\GenerateProductCombinationsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Product\Generator\CombinationGeneratorInterface;
use PrestaShopException;
use Product;

/**
 * Handles @see GenerateProductCombinationsCommand using legacy object model
 */
final class GenerateProductCombinationsHandler extends AbstractProductHandler implements GenerateProductCombinationsHandlerInterface
{
    /**
     * @var CombinationGeneratorInterface
     */
    private $combinationGenerator;

    /**
     * {@inheritdoc}
     */
    public function handle(GenerateProductCombinationsCommand $command): array
    {
        $product = $this->getProduct($command->getProductId());
        $generatedCombinations = $this->combinationGenerator->generate($command->getGroupedAttributeIds());

        return $this->addCombinations($product, $generatedCombinations);
    }

    /**
     * @param Product $product
     * @param Iterator $generatedCombinations
     *
     * @return CombinationId[]
     *
     * @throws CombinationException
     */
    private function addCombinations(Product $product, Iterator $generatedCombinations): array
    {
        $addedCombinationIds = [];
        foreach ($generatedCombinations as $generatedCombination) {
            $combinationExists = $product->productAttributeExists($generatedCombination, false, null, true);

            if ($combinationExists) {
                continue;
            }

            try {
                $addedCombinationIds[] = $this->addCombination($product, $generatedCombination);
            } catch (PrestaShopException $e) {
                throw new CombinationException(
                    sprintf(
                        'Failed to add combination for product #%d. Combination: %s',
                        $product->id,
                        var_export($generatedCombination, true)
                    )
                );
            }
        }

        return $addedCombinationIds;
    }

    /**
     * @param Product $product
     * @param int[] $generatedCombination
     *
     * @return CombinationId
     */
    private function addCombination(Product $product, array $generatedCombination): CombinationId
    {
        $newCombination = new Combination();
        $newCombination->id_product = $product->id;
        $newCombination->default_on = 0;
        $product->setAvailableDate();

        $combinationId = (int) $newCombination->add();
        $this->saveProductAttributeAssociation($combinationId, $generatedCombination);

        return new CombinationId($combinationId);
    }

    /**
     * @param int $combinationId
     * @param array $attributeIds
     */
    private function saveProductAttributeAssociation(int $combinationId, array $attributeIds): void
    {
        $attributesList = [];
        foreach ($attributeIds as $attributeId) {
            $attributesList[] = [
                'id_product_attribute' => $combinationId,
                'id_attribute' => $attributeId,
            ];
        }

        Db::getInstance()->insert('product_attribute_combination', $attributesList);
    }
}
