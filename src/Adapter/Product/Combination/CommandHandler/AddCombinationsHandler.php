<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use Combination;
use Db;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\AddCombinationsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\AddCombinationsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Product\Generator\CombinationGeneratorInterface;
use PrestaShop\PrestaShop\Core\Product\Generator\GeneratedCombination;
use PrestaShopException;
use Product;
use SpecificPriceRule;

final class AddCombinationsHandler extends AbstractProductHandler implements AddCombinationsHandlerInterface
{
    /**
     * @var CombinationGeneratorInterface
     */
    private $combinationGenerator;

    /**
     * @param CombinationGeneratorInterface $combinationGenerator
     */
    public function __construct(CombinationGeneratorInterface $combinationGenerator)
    {
        $this->combinationGenerator = $combinationGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(AddCombinationsCommand $command): array
    {
        $product = $this->getProduct($command->getProductId());
        $attributesByGroup = $command->getAttributesByGroup();

        SpecificPriceRule::disableAnyApplication();

        $generatedCombinations = $this->combinationGenerator->bulkGenerate($attributesByGroup);
        $combinationIds = $this->addToDatabase($generatedCombinations, $product);

        Product::updateDefaultAttribute($product->id);
        SpecificPriceRule::enableAnyApplication();
        SpecificPriceRule::applyAllRules([(int) $product->id]);

        return $combinationIds;
    }

    /**
     * @param GeneratedCombination[] $generatedCombinations
     * @param Product $product
     *
     * @return CombinationId[]
     */
    private function addToDatabase(array $generatedCombinations, Product $product): array
    {
        $addedCombinationsIds = [];
        foreach ($generatedCombinations as $generatedCombination) {
            // checks if combination already exist. @todo: should be possible to optimize this check.
            $existingCombinationId = $product->productAttributeExists(
                $generatedCombination->getAttributeIds(),
                false,
                null,
                true,
                true
            );

            if ($existingCombinationId) {
                //skip if combination already exists
                continue;
            }

            $newCombination = new Combination();
            $newCombination->id_product = $product->id;
            $newCombination->default_on = 0;
            $product->setAvailableDate();

            $dbInstance = Db::getInstance();
            $dbInstance->execute('START TRANSACTION');

            try {

                if (!$newCombination->add()) {
                    throw new CombinationException('Failed to add one of combinations to database');
                }
            } catch (PrestaShopException $e) {
                throw new CombinationException(
                    'An error occurred when adding combination to database',
                    0,
                    $e
                );
            }

            $this->saveCombinationAttributesAssociation($generatedCombination, $newCombination, $dbInstance);
            $dbInstance->execute('COMMIT');

            $addedCombinationsIds[] = $newCombination->id;
        }
        //@todo; AdminAttributeGeneratorController->processGenerate differs from its AdminAttributeGeneratorControllerWrapper.,
        //@todo: it used to call more methods like setAttributesImpacts(). Check that.

        return $addedCombinationsIds;
    }

    /**
     * @param GeneratedCombination $generatedCombination
     * @param Combination $combination
     *
     * @param Db $dbInstance
     */
    private function saveCombinationAttributesAssociation(
        GeneratedCombination $generatedCombination,
        Combination $combination,
        Db $dbInstance
    ) {

        $attributeList = [];
        foreach ($generatedCombination->getAttributeIds() as $attributeId) {
            $attributeList[] = [
                'id_product_attribute' => (int) $combination->id,
                'id_attribute' => (int) $attributeId,
            ];
        }

        try {
            if (!$dbInstance->insert('product_attribute_combination', $attributeList)) {
                $dbInstance->execute('REVERT');

                throw new CombinationException('Failed to save combination attributes association.');
            }
        } catch (PrestaShopException $e) {
            $dbInstance->execute('REVERT');

            throw new CombinationException('Error occurred when saving combination attributes association.');
        }
    }
}
