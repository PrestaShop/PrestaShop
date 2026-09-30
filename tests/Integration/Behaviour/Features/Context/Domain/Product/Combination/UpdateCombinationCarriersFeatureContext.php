<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use Behat\Gherkin\Node\TableNode;
use Cache;
use Carrier;
use Cart;
use Combination;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationCarriersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShopBundle\Entity\FeatureFlag;
use Product;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractShippingFeatureContext;

class UpdateCombinationCarriersFeatureContext extends AbstractShippingFeatureContext
{
    /**
     * @AfterFeature @update-combination-carriers
     */
    public static function disableCombinationFeatureValues(): void
    {
        $entityManager = CommonFeatureContext::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->getRepository(FeatureFlag::class)->findOneBy(['name' => FeatureFlagSettings::FEATURE_FLAG_COMBINATION_FEATURE_VALUES])->disable();
        $entityManager->flush();
    }

    /**
     * @When I assign combination :combinationReference with following carriers:
     */
    public function setCombinationCarriers(string $combinationReference, TableNode $table): void
    {
        $this->setCarriers($combinationReference, array_keys($table->getRowsHash()), $this->getDefaultShopId());
    }

    /**
     * @When I assign combination :combinationReference with following carriers for shop :shopReference:
     */
    public function setCombinationCarriersForShop(string $combinationReference, string $shopReference, TableNode $table): void
    {
        $this->setCarriers($combinationReference, array_keys($table->getRowsHash()), $this->referenceToId($shopReference));
    }

    /**
     * @When I remove all carriers from combination :combinationReference
     */
    public function removeCombinationCarriers(string $combinationReference): void
    {
        $this->setCarriers($combinationReference, [], $this->getDefaultShopId());
    }

    /**
     * @Then combination :combinationReference should have carriers :carrierReferences
     *
     * @param string[] $carrierReferences
     */
    public function assertCombinationCarriers(string $combinationReference, array $carrierReferences): void
    {
        $this->assertCarriersForShop($combinationReference, $carrierReferences, $this->getDefaultShopId());
    }

    /**
     * @Then combination :combinationReference should have carriers :carrierReferences for shop :shopReference
     *
     * @param string[] $carrierReferences
     */
    public function assertCombinationCarriersForShop(string $combinationReference, array $carrierReferences, string $shopReference): void
    {
        $this->assertCarriersForShop($combinationReference, $carrierReferences, $this->referenceToId($shopReference));
    }

    /**
     * @Then the carriers available for combination :combinationReference should be :carrierReferences
     *
     * @param string[] $carrierReferences
     */
    public function assertAvailableCarriers(string $combinationReference, array $carrierReferences): void
    {
        Cache::clean('Carrier::getAvailableCarrierList_*');
        $combination = new Combination($this->referenceToId($combinationReference));
        $error = [];

        Assert::assertEqualsCanonicalizing(
            array_map(fn (string $carrierReference): int => $this->referenceToId($carrierReference), $carrierReferences),
            Carrier::getAvailableCarrierList(new Product((int) $combination->id_product), 0, null, $this->getDefaultShopId(), new Cart(), $error, (int) $combination->id)
        );
    }

    /**
     * @param string[] $carrierReferences
     */
    private function setCarriers(string $combinationReference, array $carrierReferences, int $shopId): void
    {
        $this->getCommandBus()->handle(new SetCombinationCarriersCommand(
            $this->referenceToId($combinationReference),
            $this->getCarrierReferenceIds($carrierReferences),
            ShopConstraint::shop($shopId)
        ));
    }

    /**
     * @param string[] $carrierReferences
     */
    private function assertCarriersForShop(string $combinationReference, array $carrierReferences, int $shopId): void
    {
        Assert::assertEqualsCanonicalizing(
            $this->getCarrierReferenceIds($carrierReferences),
            $this->getQueryBus()->handle(new GetCombinationForEditing(
                $this->referenceToId($combinationReference),
                ShopConstraint::shop($shopId)
            ))->getCarrierReferenceIds()
        );
    }
}
