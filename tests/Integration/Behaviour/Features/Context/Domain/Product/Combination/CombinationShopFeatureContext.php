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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationShopAssociationNotFoundException;

class CombinationShopFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @Then combinations :combinationReferences are not associated to shop(s) :shopReferences
     *
     * @param string $combinationReferences
     * @param string $shopReferences
     */
    public function checkNoShopAssociation(string $combinationReferences, string $shopReferences): void
    {
        foreach ($this->referencesToIds($shopReferences) as $shopId) {
            foreach (explode(',', $combinationReferences) as $combinationReference) {
                $caughtException = null;
                try {
                    $this->getCombinationForEditing($combinationReference, $shopId);
                } catch (CombinationShopAssociationNotFoundException $e) {
                    // We catch CombinationShopAssociationNotFoundException specifically because it is thrown first if the combination association is not thrown
                    // If it is present but not the product association ProductShopAssociationNotFoundException but it only ensure product's association was removed
                    // not that all combinations associations have been correctly cleared
                    $caughtException = $e;
                }

                Assert::assertNotNull($caughtException);
            }
        }
    }

    /**
     * @Then /^combinations "(.*)" are associated to shop "(.*)"$/
     *
     * @param string $combinationReferences
     * @param string $shopReference
     */
    public function checkShopAssociation(string $combinationReferences, string $shopReference): void
    {
        $shopId = $this->getSharedStorage()->get($shopReference);

        foreach (explode(',', $combinationReferences) as $combinationReference) {
            $caughtException = null;
            try {
                $this->getCombinationForEditing($combinationReference, $shopId);
            } catch (CombinationShopAssociationNotFoundException $e) {
                $caughtException = $e;
            }

            Assert::assertNull($caughtException);
        }
    }
}
