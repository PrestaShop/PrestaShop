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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Configuration;
use Exception;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\EditFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureForEditing;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryResult\EditableFeature;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use RuntimeException;

class FeatureFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I create product feature :reference with specified properties:
     */
    public function createFeature($reference, TableNode $node)
    {
        $properties = $node->getRowsHash();
        $featureId = $this->createProductFeature($properties['name']);

        $this->getSharedStorage()->set($reference, (int) $featureId->getValue());
    }

    /**
     * @Then /^product feature "([^"]*)" name should be "([^"]*)"$/
     */
    public function productFeatureNameShouldBe($reference, $name)
    {
        $productFeatureId = $this->getSharedStorage()->get($reference);
        $this->productFeatureWithIdNameShouldBe($productFeatureId, $name);
    }

    /**
     * @Given /^product feature with id "([^"]*)" exists$/
     */
    public function productFeatureWithIdExists($featureId)
    {
        $this->getQueryBus()->handle(new GetFeatureForEditing((int) $featureId));
    }

    /**
     * @Given /^product feature with reference "([^"]*)" exists$/
     */
    public function productFeatureWithReferenceExists(string $featureReference): void
    {
        $productFeatureId = $this->getSharedStorage()->get($featureReference);
        $this->getQueryBus()->handle(new GetFeatureForEditing($productFeatureId));
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
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I update product feature with reference "([^"]*)" field "name" in default language to "([^"]*)"$/
     */
    public function iUpdateProductFeatureWithReferenceNameTo(string $featureReference, string $featureName): void
    {
        $productFeatureId = $this->getSharedStorage()->get($featureReference);
        $this->iUpdateProductFeatureWithIdNameTo($productFeatureId, $featureName);
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
            throw new RuntimeException(sprintf('Product feature with id "%s" has name "%s", but "%s" was expected', $featureId, $featureNames[$defaultLanguageId], $featureName));
        }
    }

    /**
     * @Then /^product feature with reference "([^"]*)" field "name" in default language should be "([^"]*)"$/
     */
    public function productFeatureWithReferenceNameShouldBe(string $featureReference, string $featureName): void
    {
        $productFeatureId = $this->getSharedStorage()->get($featureReference);
        $this->productFeatureWithIdNameShouldBe($productFeatureId, $featureName);
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
            $this->setLastException($e);
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
}
