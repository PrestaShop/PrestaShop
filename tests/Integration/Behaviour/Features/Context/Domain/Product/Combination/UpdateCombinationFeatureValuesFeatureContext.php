<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\Command\RemoveAllFeatureValuesFromCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\Command\SetCombinationFeatureValuesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\Query\GetCombinationFeatureValues;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\QueryResult\CombinationFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\DuplicateFeatureValueAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\InvalidAssociatedFeatureException;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractProductFeatureContext;

class UpdateCombinationFeatureValuesFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I set to combination :combinationReference the following feature values:
     *
     * @param string $combinationReference
     * @param TableNode $table
     */
    public function setCombinationFeatureValues(string $combinationReference, TableNode $table): void
    {
        $featuresData = $table->getColumnsHash();
        $combinationFeatures = [];
        foreach ($featuresData as $featuresDatum) {
            $combinationFeature = ['feature_id' => $this->referenceToId($featuresDatum['feature'])];
            if (!empty($featuresDatum['feature_value'])) {
                $combinationFeature['feature_value_id'] = $this->referenceToId($featuresDatum['feature_value']);
            }
            if (!empty($featuresDatum['custom_values'])) {
                $combinationFeature['custom_values'] = $this->localizeByCell($featuresDatum['custom_values']);
            }

            $combinationFeatures[] = $combinationFeature;
        }
        $command = new SetCombinationFeatureValuesCommand($this->referenceToId($combinationReference), $combinationFeatures);
        try {
            $featureIds = $this->getCommandBus()->handle($command);
            if (count($featureIds) !== count($combinationFeatures)) {
                throw new RuntimeException(sprintf(
                    'Incorrect number of feature ids returned for combination %s, expected %d but got %d instead',
                    $combinationReference,
                    count($featureIds),
                    count($combinationFeatures)
                ));
            }
            $this->storeCreatedFeatureValuesReferences($featureIds, $featuresData);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I remove all feature values from combination :combinationReference
     *
     * @param string $combinationReference
     */
    public function removeAllFeatureValuesFromCombination(string $combinationReference): void
    {
        $this->getCommandBus()->handle(new RemoveAllFeatureValuesFromCombinationCommand($this->referenceToId($combinationReference)));
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

                $featureId = $this->referenceToId($featureValueDatum['feature']);
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
     * @Then combination :combinationReference should have following feature values:
     *
     * @param string $combinationReference
     * @param TableNode $table
     */
    public function assertCombinationFeatureValues(string $combinationReference, TableNode $table): void
    {
        $query = new GetCombinationFeatureValues(
            $this->referenceToId($combinationReference),
            $this->getDefaultShopId()
        );
        /** @var CombinationFeatureValue[] $combinationFeatureValues */
        $combinationFeatureValues = $this->getQueryBus()->handle($query);

        $expectedFeatureValues = $table->getColumnsHash();
        if (count($combinationFeatureValues) !== count($expectedFeatureValues)) {
            throw new RuntimeException(sprintf(
                'Incorrect number of feature values for combination %s, expected %d but got %d instead',
                $combinationReference,
                count($expectedFeatureValues),
                count($combinationFeatureValues)
            ));
        }

        foreach ($expectedFeatureValues as $key => $expectedFeatureValue) {
            // If a new custom value is found (e.g. after a duplication) store its new reference and use
            // it as the expected feature value reference for the assertion loop below.
            if (!empty($expectedFeatureValue['new_feature_value']) && !empty($expectedFeatureValue['custom_values'])) {
                $localizedValues = $this->localizeByCell($expectedFeatureValue['custom_values']);
                foreach ($combinationFeatureValues as $combinationFeatureValue) {
                    if ($localizedValues === $combinationFeatureValue->getLocalizedValues()) {
                        $this->getSharedStorage()->set($expectedFeatureValue['new_feature_value'], $combinationFeatureValue->getFeatureValueId());
                        $expectedFeatureValues[$key]['feature_value'] = $expectedFeatureValue['new_feature_value'];
                    }
                }
            }
        }

        foreach ($expectedFeatureValues as $expectedFeatureValue) {
            $foundMatchingFeatureValue = false;
            $expectedFeatureId = $this->referenceToId($expectedFeatureValue['feature']);
            $expectedFeatureValueId = $this->referenceToId($expectedFeatureValue['feature_value']);
            foreach ($combinationFeatureValues as $combinationFeatureValue) {
                if ($expectedFeatureId !== $combinationFeatureValue->getFeatureId()) {
                    continue;
                }
                if ($expectedFeatureValueId !== $combinationFeatureValue->getFeatureValueId()) {
                    continue;
                }
                $foundMatchingFeatureValue = true;
                if (!empty($expectedFeatureValue['custom_values'])) {
                    Assert::assertTrue($combinationFeatureValue->isCustom());
                    $localizedValues = $this->localizeByCell($expectedFeatureValue['custom_values']);
                    Assert::assertEquals($localizedValues, $combinationFeatureValue->getLocalizedValues());
                } else {
                    Assert::assertFalse($combinationFeatureValue->isCustom());
                }
            }
            if (!$foundMatchingFeatureValue) {
                throw new RuntimeException(sprintf(
                    'Could not find feature value %s from feature %s in combination %s',
                    $expectedFeatureValue['feature_value'],
                    $expectedFeatureValue['feature'],
                    $combinationReference
                ));
            }
        }
    }

    /**
     * @Then combination :combinationReference should have no feature values
     *
     * @param string $combinationReference
     */
    public function assertCombinationHasNoFeatureValues(string $combinationReference): void
    {
        $query = new GetCombinationFeatureValues(
            $this->referenceToId($combinationReference),
            $this->getDefaultShopId()
        );
        /** @var CombinationFeatureValue[] $combinationFeatureValues */
        $combinationFeatureValues = $this->getQueryBus()->handle($query);

        if (!empty($combinationFeatureValues)) {
            throw new RuntimeException(sprintf(
                'Expected combination %s to have no feature values but got %d instead',
                $combinationReference,
                count($combinationFeatureValues)
            ));
        }
    }

    /**
     * @Then I should get an error that a combination feature can only be associated once
     */
    public function assertDuplicateException(): void
    {
        $this->assertLastErrorIs(DuplicateFeatureValueAssociationException::class);
    }

    /**
     * @Then I should get an error that a combination feature value cannot be associated to another feature
     */
    public function assertInvalidFeatureAssociation(): void
    {
        $this->assertLastErrorIs(InvalidAssociatedFeatureException::class);
    }
}
