<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
