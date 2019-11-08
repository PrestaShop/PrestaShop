<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Configuration;
use Exception;
use Feature;
use FeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\EditFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\FeatureValue\Command\AddFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureForEditing;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryResult\EditableFeature;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Domain\Language\FeatureValueId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class FeatureFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I create product feature :reference with specified properties:
     */
    public function createFeature($reference, TableNode $node)
    {
        $properties = $node->getRowsHash();
        $featureId = $this->createProductFeature($properties['name']);

        SharedStorage::getStorage()->set($reference, new Feature($featureId->getValue()));
    }

    /**
     * @Then /^product feature "([^"]*)" name should be "([^"]*)"$/
     */
    public function productFeatureNameShouldBe($reference, $name)
    {
        $defaultLanguageId = Configuration::get('PS_LANG_DEFAULT');
        /** @var Feature $productFeature */
        $productFeature = SharedStorage::getStorage()->get($reference);

        if ($productFeature->name[$defaultLanguageId] !== $name) {
            throw new RuntimeException(
                sprintf(
                    'Feature "%s" name was expected to be "%s" but is "%s"',
                    $reference,
                    $name,
                    $productFeature->name[$defaultLanguageId]
                )
            );
        }
    }

    /**
     * @Given /^product feature with id "([^"]*)" exists$/
     */
    public function productFeatureWithIdExists($featureId)
    {
        $this->getQueryBus()->handle(new GetFeatureForEditing((int) $featureId));
    }

    /**
     * @When /^I update product feature with id "([^"]*)" field "name" in default language to "([^"]*)"$/
     */
    public function iUpdateProductFeatureWithIdNameTo($featureId, $featureName)
    {
        $defaultLanguageId = Configuration::get('PS_LANG_DEFAULT');

        /** @var EditableFeature $editableFeature */
        $editableFeature = $this->getQueryBus()->handle(new GetFeatureForEditing((int) $featureId));
        $featureNames = $editableFeature->getName();
        $featureNames[$defaultLanguageId] = $featureName;

        try {
            $editFeatureCommand = new EditFeatureCommand($featureId);
            $editFeatureCommand->setLocalizedNames($featureNames);

            $this->getCommandBus()->handle($editFeatureCommand);
        } catch (Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @Then /^product feature with id "([^"]*)" field "name" in default language should be "([^"]*)"$/
     */
    public function productFeatureWithIdNameShouldBe($featureId, $featureName)
    {
        $defaultLanguageId = Configuration::get('PS_LANG_DEFAULT');

        /** @var EditableFeature $editableFeature */
        $editableFeature = $this->getQueryBus()->handle(new GetFeatureForEditing($featureId));
        $featureNames = $editableFeature->getName();

        if ($featureNames[$defaultLanguageId] !== $featureName) {
            throw new RuntimeException(sprintf(
                'Product feature with id "%s" has name "%s", but "%s" was expected',
                $featureId,
                $featureNames[$defaultLanguageId],
                $featureName
            ));
        }
    }

    /**
     * @Then /^I should get an error that feature name is invalid\.$/
     */
    public function iShouldGetAnErrorThatFeatureNameIsInvalid()
    {
        $this->assertLastErrorIs(FeatureConstraintException::class);
    }

    /**
     * @When /^I create product feature with empty name$/
     */
    public function iCreateProductFeatureWithEmptyName1()
    {
        try {
            $this->createProductFeature('');
        } catch (Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @param string $nameInDefaultLanguage
     *
     * @return FeatureId
     */
    private function createProductFeature($nameInDefaultLanguage)
    {
        $defaultLanguageId = Configuration::get('PS_LANG_DEFAULT');

        $command = new AddFeatureCommand(
            [$defaultLanguageId => $nameInDefaultLanguage]
        );

        return $this->getCommandBus()->handle($command);
    }

    /**
     * @param string $valueOfFeatureValue
     * @param int $featureId
     *
     * @return FeatureValueId
     */
    private function createFeatureValue(string $valueOfFeatureValue, int $featureId)
    {
        $defaultLanguageId = Configuration::get('PS_LANG_DEFAULT');

        $command = new AddFeatureValueCommand($featureId, [
            $defaultLanguageId => $valueOfFeatureValue,
        ]);

        return $this->getCommandBus()->handle($command);
    }

    /**
     * @When I create feature value :feature_value_reference for product feature :feature_reference with specified properties:
     */
    public function iCreateFeatureValueForProductFeatureWithSpecifiedProperties(
        $featureValueReference,
        $featureReference,
        TableNode $node
    ) {
        /** @var Feature $feature */
        $feature = SharedStorage::getStorage()->get($featureReference);
        $properties = $node->getRowsHash();
        $featureValueId = $this->createFeatureValue($properties['value'], (int) $feature->id);

        SharedStorage::getStorage()->set($featureValueReference, new FeatureValue($featureValueId->getValue()));
    }

    /**
     * @Then product feature :feature_reference should have feature value :feature_value_reference
     */
    public function productFeatureShouldHaveFeatureValue($featureReference, $featureValueReference)
    {
        /** @var Feature $feature */
        $feature = SharedStorage::getStorage()->get($featureReference);

        /** @var FeatureValue $featureValue */
        $featureValue = SharedStorage::getStorage()->get($featureValueReference);

        foreach (FeatureValue::getFeatureValues($feature->id) as $value) {
            if ($featureValue->id == $value['id_feature_value']) {
                return;
            }
        }

        throw new RuntimeException(sprintf(
            'Product feature with id "%s" does not have feature value with id "%s"',
            $feature->id,
            $featureValue->id
        ));
    }

    /**
     * @Then feature value :feature_value_reference value should be :value_of_feature_value
     */
    public function featureValueNameShouldBe($featureValueReference, $valueOfFeatureValue)
    {
        /** @var FeatureValue $featureValue */
        $featureValue = SharedStorage::getStorage()->get($featureValueReference);
        $defaultLanguageId = Configuration::get('PS_LANG_DEFAULT');

        if (!isset($featureValue->value[$defaultLanguageId]) || $featureValue->value[$defaultLanguageId] !== $valueOfFeatureValue) {
            throw new RuntimeException(sprintf(
                'Feature value with id "%s" has value "%s", but "%s" was expected',
                $featureValue->id,
                $featureValue->value[$defaultLanguageId],
                $valueOfFeatureValue
            ));
        }
    }
}
