<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Carrier;

use Behat\Gherkin\Node\TableNode;
use Carrier;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\SetCarrierRangesCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierRanges;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\CarrierRangesCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Zone;

class CarrierRangesFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Then I set ranges for carrier :reference with specified properties for all shops:
     */
    public function setCarrierRangesAllShopsWithoutIdUpdate(string $reference, TableNode $node): void
    {
        $this->setCarrierRanges($reference, null, ShopConstraint::allShops(), $node);
    }

    /**
     * @Then I set ranges for carrier :reference with specified properties for all shops I get a new carrier referenced as :newReference:
     */
    public function setCarrierRangesAllShops(string $reference, string $newReference, TableNode $node): void
    {
        $this->setCarrierRanges($reference, $newReference, ShopConstraint::allShops(), $node);
    }

    /**
     * @Then I set ranges for carrier :reference with specified properties for shop :shopReference:
     */
    public function setCarrierRangesShopWithoutIdUpdate(string $reference, string $shopReference, TableNode $node): void
    {
        $this->setCarrierRanges($reference, null, $this->getShopConstraint($shopReference), $node);
    }

    /**
     * @Then I set ranges for carrier :reference called :newReference with specified properties for shop :shopReference:
     */
    public function setCarrierRangesShop(string $reference, string $newReference, string $shopReference, TableNode $node): void
    {
        $this->setCarrierRanges($reference, $newReference, $this->getShopConstraint($shopReference), $node);
    }

    /**
     * @Then carrier :reference should have the following ranges for all shops:
     */
    public function getCarrierRangesAllShops(string $reference, TableNode $node): void
    {
        $this->getCarrierRanges($reference, ShopConstraint::allShops(), $node);
    }

    /**
     * @Then carrier :reference should have the following ranges for shop :shop:
     */
    public function getCarrierRangesShop(string $reference, string $shopReference, TableNode $node): void
    {
        $this->getCarrierRanges($reference, $this->getShopConstraint($shopReference), $node);
    }

    private function setCarrierRanges(string $reference, ?string $newReference, ShopConstraint $shopConstraint, TableNode $node): void
    {
        try {
            $initialCarrierId = $this->referenceToId($reference);
            $data = $node->getColumnsHash();

            foreach ($data as &$range) {
                try {
                    /** @var Zone $zone */
                    $zone = $this->getSharedStorage()->get($range['id_zone']);
                    $range['id_zone'] = $zone->id;
                } catch (RuntimeException $e) {
                    $this->setLastException(new CarrierConstraintException(
                        sprintf('Invalid zone id reference %d supplied. Zone id must be a positive integer.', $range['id_zone']),
                        CarrierConstraintException::INVALID_ZONE_ID
                    ));
                }
            }

            $command = new SetCarrierRangesCommand($initialCarrierId, $data, $shopConstraint);

            $carrierId = $this->getCommandBus()->handle($command);
            if ($newReference) {
                Assert::assertNotEquals($initialCarrierId, $carrierId->getValue(), 'Carrier ID was expected to be updated');
                $this->getSharedStorage()->set($newReference, $carrierId->getValue());
            } else {
                Assert::assertEquals($initialCarrierId, $carrierId->getValue(), 'Carrier ID was expected the remain the same');
            }

            // Reset cache so that the carrier becomes selectable
            Carrier::resetStaticCache();
        } catch (CarrierException $e) {
            $this->setLastException($e);
        }
    }

    private function getCarrierRanges(string $reference, ShopConstraint $shopConstraint, TableNode $node): void
    {
        try {
            $carrierId = $this->referenceToId($reference);

            $command = new GetCarrierRanges($carrierId, $shopConstraint);

            $rangesDatabase = $this->getCommandBus()->handle($command);
            $data = $node->getColumnsHash();
            $zoneIds = [];
            foreach ($data as &$range) {
                /** @var Zone $zone */
                $zone = $this->referencesToIds($range['id_zone'])[0];
                $range['id_zone'] = $zone->id;
                $zoneIds[] = $zone->id;
            }
            $rangesExpected = new CarrierRangesCollection($data);

            Assert::assertEquals($rangesExpected, $rangesDatabase);
        } catch (CarrierException $e) {
            $this->setLastException($e);
        }
    }

    private function getShopConstraint(string $shopReference): ShopConstraint
    {
        return ShopConstraint::shop((int) $this->referenceToId($shopReference));
    }
}
