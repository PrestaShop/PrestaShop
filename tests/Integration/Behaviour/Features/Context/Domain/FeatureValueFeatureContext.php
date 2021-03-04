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
use Language;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\EditFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureValueConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureValueNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureValueForEditing;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryResult\EditableFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Transform\LocalizedArrayTransformContext;

class FeatureValueFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I create feature value :featureValueReference for feature :featureReference with following properties:
     *
     * @param string $featureValueReference
     * @param string $featureReference
     * @param TableNode $table
     */
    public function createFeatureValue(string $featureValueReference, string $featureReference, TableNode $table): void
    {
        $featureId = $this->getSharedStorage()->get($featureReference);
        $data = $this->localizeByRows($table);

        $this->cleanLastException();
        $command = new AddFeatureValueCommand($featureId, $data['value']);
        try {
            /** @var FeatureValueId $featureValueId */
            $featureValueId = $this->getCommandBus()->handle($command);
            $this->getSharedStorage()->set($featureValueReference, $featureValueId->getValue());
        } catch (FeatureValueException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit feature value :featureValueReference with following properties:
     *
     * @param string $featureValueReference
     * @param TableNode $table
     */
    public function editFeatureValue(string $featureValueReference, TableNode $table): void
    {
        $featureValueId = $this->getSharedStorage()->get($featureValueReference);
        $data = $this->localizeByRows($table);

        $command = new EditFeatureValueCommand($featureValueId);
        $command->setLocalizedValues($data['value']);

        $this->cleanLastException();
        try {
            $this->getCommandBus()->handle($command);
        } catch (FeatureValueException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then feature value :featureValueReference localized value should be:
     *
     * localizedValues transformation handled by @see LocalizedArrayTransformContext
     *
     * @param string $featureValueReference
     * @param array $expectedLocalizedValues
     */
    public function assertFeatureValue(string $featureValueReference, array $expectedLocalizedValues): void
    {
        $featureValueId = $this->getSharedStorage()->get($featureValueReference);
        /** @var EditableFeatureValue $editableFeatureValue */
        $editableFeatureValue = $this->getQueryBus()->handle(new GetFeatureValueForEditing($featureValueId));
        $actualValues = $editableFeatureValue->getLocalizedValues();

        foreach ($expectedLocalizedValues as $langId => $expectedValue) {
            $langIso = Language::getIsoById($langId);

            if (!isset($actualValues[$langId])) {
                throw new RuntimeException(sprintf(
                    'Expected localized feature value is not set in %s language',
                    $langIso
                ));
            }

            $actualValue = $actualValues[$langId];
            if ($expectedValue !== $actualValue) {
                throw new RuntimeException(
                    sprintf(
                        'Expected feature value in "%s" language was "%s", but got "%s"',
                        $langIso,
                        var_export($expectedValue, true),
                        var_export($actualValue, true)
                    )
                );
            }
        }
    }

    /**
     * @When I associate feature value :featureValueReference to feature :featureReference
     *
     * @param string $featureValueReference
     * @param string $featureReference
     */
    public function associateFeatureValueToFeature(string $featureValueReference, string $featureReference): void
    {
        $featureValueId = $this->getSharedStorage()->get($featureValueReference);
        $featureId = $this->getSharedStorage()->get($featureReference);

        $command = new EditFeatureValueCommand($featureValueId);
        $command->setFeatureId($featureId);

        $this->cleanLastException();
        try {
            $this->getCommandBus()->handle($command);
        } catch (FeatureValueException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then feature value :featureValueReference should be associated to feature :featureReference
     *
     * @param string $featureValueReference
     * @param string $featureReference
     */
    public function assertFeatureValueAssociation(string $featureValueReference, string $featureReference): void
    {
        $featureValueId = $this->getSharedStorage()->get($featureValueReference);
        $featureId = $this->getSharedStorage()->get($featureReference);

        /** @var EditableFeatureValue $editableFeatureValue */
        $editableFeatureValue = $this->getQueryBus()->handle(new GetFeatureValueForEditing($featureValueId));
        if ($editableFeatureValue->getFeatureId()->getValue() !== $featureId) {
            throw new RuntimeException(sprintf(
                'Incorrect feature associated to %s, expected %d but got %d',
                $featureReference,
                $featureId,
                $editableFeatureValue->getFeatureId()
            ));
        }
    }

    /**
     * @Then I should get an error that feature value is invalid
     */
    public function iShouldGetAnErrorThatFeatureValueIsInvalid(): void
    {
        $this->assertLastErrorIs(
            FeatureValueConstraintException::class,
            FeatureValueConstraintException::INVALID_VALUE
        );
    }

    /**
     * @Then feature value :featureValueReference should not exist
     *
     * @param string $featureValueReference
     */
    public function assertFeatureValueDoesNotExist(string $featureValueReference): void
    {
        $featureValueId = $this->getSharedStorage()->get($featureValueReference);
        $caughtException = null;
        try {
            $this->getQueryBus()->handle(new GetFeatureValueForEditing($featureValueId));
        } catch (FeatureValueNotFoundException $e) {
            $caughtException = $e;
        }

        if (null === $caughtException) {
            throw new RuntimeException(sprintf(
                'Feature value %s was expected not to be found',
                $featureValueReference
            ));
        }
    }
}
