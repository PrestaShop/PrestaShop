<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\RemoveAllFeatureValuesFromProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\SetProductFeatureValuesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\DuplicateFeatureValueAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\InvalidAssociatedFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Query\GetProductFeatureValues;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\QueryResult\ProductFeatureValue;
use RuntimeException;

class UpdateProductFeatureValuesFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I set to product :productReference the following feature values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function setProductFeatureValues(string $productReference, TableNode $table): void
    {
        $featuresData = $table->getColumnsHash();
        $productFeatures = [];
        foreach ($featuresData as $featuresDatum) {
            $productFeature = ['feature_id' => $this->getSharedStorage()->get($featuresDatum['feature'])];
            if (!empty($featuresDatum['feature_value'])) {
                $productFeature['feature_value_id'] = $this->getSharedStorage()->get($featuresDatum['feature_value']);
            }
            if (!empty($featuresDatum['custom_values'])) {
                $productFeature['custom_values'] = $this->localizeByCell($featuresDatum['custom_values']);
            }

            $productFeatures[] = $productFeature;
        }
        $command = new SetProductFeatureValuesCommand($this->getSharedStorage()->get($productReference), $productFeatures);
        try {
            $featureIds = $this->getCommandBus()->handle($command);
            if (count($featureIds) !== count($productFeatures)) {
                throw new RuntimeException(sprintf(
                    'Incorrect number of feature ids returned for product %s, expected %d but got %d instead',
                    $productReference,
                    count($featureIds),
                    count($productFeatures)
                ));
            }
            $this->storeCreatedFeatureValuesReferences($featureIds, $featuresData);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I remove all feature values from product :productReference
     *
     * @param string $productReference
     */
    public function removeAllFeatureValuesFromProduct(string $productReference): void
    {
        $this->getCommandBus()->handle(new RemoveAllFeatureValuesFromProductCommand($this->getSharedStorage()->get($productReference)));
    }

    /**
     * @param FeatureValueId[] $featureValueIds
     * @param array $featureValuesData
     */
    private function storeCreatedFeatureValuesReferences(array $featureValueIds, array $featureValuesData): void
    {
        /** @var FeatureValueRepository $featureValueRepository */
        $featureValueRepository = $this->getContainer()->get(FeatureValueRepository::class);
        foreach ($featureValueIds as $featureValueId) {
            $featureValue = $featureValueRepository->get($featureValueId);
            foreach ($featureValuesData as $featureValueDatum) {
                if (empty($featureValueDatum['custom_reference'])
                    || $this->getSharedStorage()->exists($featureValueDatum['custom_reference'])) {
                    continue;
                }

                $featureId = (int) $this->getSharedStorage()->get($featureValueDatum['feature']);
                if ((int) $featureValue->id_feature !== $featureId) {
                    continue;
                }

                $localizedValues = $this->localizeByCell($featureValueDatum['custom_values']);
                if ($featureValue->value === $localizedValues) {
                    $this->getSharedStorage()->set($featureValueDatum['custom_reference'], $featureValueId->getValue());
                    continue 2;
                }
            }
        }
    }

    /**
     * @Then product :productReference should have following feature values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertProductFeatureValues(string $productReference, TableNode $table): void
    {
        $query = new GetProductFeatureValues(
            $this->getSharedStorage()->get($productReference),
            $this->getDefaultShopId()
        );
        /** @var ProductFeatureValue[] $productFeatureValues */
        $productFeatureValues = $this->getQueryBus()->handle($query);

        $expectedFeatureValues = $table->getColumnsHash();
        if (count($productFeatureValues) !== count($expectedFeatureValues)) {
            throw new RuntimeException(sprintf(
                'Incorrect number of feature values for product %s, expected %d but got %d instead',
                $productReference,
                count($expectedFeatureValues),
                count($productFeatureValues)
            ));
        }

        foreach ($expectedFeatureValues as $key => $expectedFeatureValue) {
            // If new custom value is found set a new reference in storage, and set this new reference as the expected one for the second loop
            if (!empty($expectedFeatureValue['new_feature_value']) && !empty($expectedFeatureValue['custom_values'])) {
                $localizedValues = $this->localizeByCell($expectedFeatureValue['custom_values']);
                foreach ($productFeatureValues as $productFeatureValue) {
                    if ($localizedValues === $productFeatureValue->getLocalizedValues()) {
                        $this->getSharedStorage()->set($expectedFeatureValue['new_feature_value'], $productFeatureValue->getFeatureValueId());
                        $expectedFeatureValues[$key]['feature_value'] = $expectedFeatureValue['new_feature_value'];
                    }
                }
            }
        }

        foreach ($expectedFeatureValues as $expectedFeatureValue) {
            $foundMatchingFeatureValue = false;
            $expectedFeatureId = $this->getSharedStorage()->get($expectedFeatureValue['feature']);
            $expectedFeatureValueId = $this->getSharedStorage()->get($expectedFeatureValue['feature_value']);
            foreach ($productFeatureValues as $productFeatureValue) {
                if ($expectedFeatureId !== $productFeatureValue->getFeatureId()) {
                    continue;
                }
                if ($expectedFeatureValueId !== $productFeatureValue->getFeatureValueId()) {
                    continue;
                }
                $foundMatchingFeatureValue = true;
                if (!empty($expectedFeatureValue['custom_values'])) {
                    Assert::assertTrue($productFeatureValue->isCustom());
                    $localizedValues = $this->localizeByCell($expectedFeatureValue['custom_values']);
                    Assert::assertEquals($localizedValues, $productFeatureValue->getLocalizedValues());
                } else {
                    Assert::assertFalse($productFeatureValue->isCustom());
                }
            }
            if (!$foundMatchingFeatureValue) {
                throw new RuntimeException(sprintf(
                    'Could not find feature value %s from feature %s in product %s',
                    $expectedFeatureValue['feature_value'],
                    $expectedFeatureValue['feature'],
                    $productReference
                ));
            }
        }
    }

    /**
     * @Then product :productReference should have no feature values
     *
     * @param string $productReference
     */
    public function assertProductHasNoFeatureValues(string $productReference): void
    {
        $query = new GetProductFeatureValues(
            $this->getSharedStorage()->get($productReference),
            $this->getDefaultShopId()
        );
        /** @var ProductFeatureValue[] $productFeatureValues */
        $productFeatureValues = $this->getQueryBus()->handle($query);

        if (!empty($productFeatureValues)) {
            throw new RuntimeException(sprintf(
                'Expected product %s to have no feature values but got %d instead',
                $productReference,
                count($productFeatureValues)
            ));
        }
    }

    /**
     * @Then I should get an error that a feature can only be associated once
     */
    public function assertDuplicateException(): void
    {
        $this->assertLastErrorIs(DuplicateFeatureValueAssociationException::class);
    }

    /**
     * @Then I should get an error that a feature value cannot be associated to another feature
     */
    public function assertInvalidFeatureAssociation(): void
    {
        $this->assertLastErrorIs(InvalidAssociatedFeatureException::class);
    }
}
