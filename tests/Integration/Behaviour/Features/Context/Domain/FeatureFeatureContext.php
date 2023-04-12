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
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\BulkDeleteFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\DeleteFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\EditFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureForEditing;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryResult\EditableFeature;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use RuntimeException;

class FeatureFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I create product feature :reference with specified properties:
     */
    public function createFeature(string $featureReference, TableNode $tableNode): void
    {
        $localizedData = $this->localizeByRows($tableNode);

        try {
            /** @var FeatureId $featureId */
            $featureId = $this->getCommandBus()->handle(new AddFeatureCommand(
                $localizedData['name'],
                isset($localizedData['associated shops']) ? $this->referencesToIds($localizedData['associated shops']) : []
            ));

            $this->getSharedStorage()->set($featureReference, $featureId->getValue());
        } catch (FeatureException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I update product feature :feature with following details:
     *
     * @param string $featureReference
     * @param TableNode $tableNode
     */
    public function updateFeature(string $featureReference, TableNode $tableNode): void
    {
        $localizedData = $this->localizeByRows($tableNode);
        $command = new EditFeatureCommand($this->getSharedStorage()->get($featureReference));

        try {
            if (isset($localizedData['name'])) {
                $command->setLocalizedNames($localizedData['name']);
            }

            if (isset($localizedData['associated shops'])) {
                $command->setAssociatedShopIds($this->referencesToIds($localizedData['associated shops']));
            }

            $this->getCommandBus()->handle($command);
        } catch (FeatureException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete product feature :featureReference
     *
     * @param string $featureReference
     */
    public function deleteFeature(string $featureReference): void
    {
        $this->getCommandBus()->handle(
            new DeleteFeatureCommand($this->getSharedStorage()->get($featureReference))
        );
    }

    /**
     * @When I bulk delete product features :featureReferences
     *
     * @param string $featureReferences
     */
    public function bulkDeleteFeatures(string $featureReferences): void
    {
        $this->getCommandBus()->handle(new BulkDeleteFeatureCommand($this->referencesToIds($featureReferences)));
    }

    /**
     * @Given product feature :featureReference exists
     * @Given product feature :featureReference should exist
     */
    public function productFeatureExists(string $featureReference): void
    {
        $editableFeature = $this->getFeatureForEditing($featureReference);

        Assert::assertSame(
            $this->getSharedStorage()->get($featureReference),
            $editableFeature->getFeatureId()->getValue()
        );
    }

    /**
     * @Then product feature :featureReference should not exist
     *
     * @param string $featureReference
     */
    public function assertFeatureDoesNotExist(string $featureReference): void
    {
        $coughtException = null;

        try {
            $this->getFeatureForEditing($featureReference);
        } catch (FeatureNotFoundException $e) {
            $coughtException = $e;
        }

        if (null === $coughtException) {
            throw new RuntimeException(sprintf(
                'Feature %s was expected not to be found',
                $featureReference
            ));
        }
    }

    /**
     * @Then product feature :featureReference should have following details:
     *
     * @param string $featureReference
     * @param TableNode $expectedData
     */
    public function assertEditableFeatureDetails(string $featureReference, TableNode $expectedData): void
    {
        $expectedLocalizedData = $this->localizeByRows($expectedData);
        $editableFeature = $this->getFeatureForEditing($featureReference);

        if (isset($expectedLocalizedData['name'])) {
            Assert::assertSame(
                $expectedLocalizedData['name'],
                $editableFeature->getName()
            );
        }

        if (isset($expectedLocalizedData['associated shops'])) {
            Assert::assertSame(
                $this->referencesToIds($expectedLocalizedData['associated shops']),
                $editableFeature->getShopAssociationIds()
            );
        }
    }

    /**
     * @Then I should get an error that feature name is invalid
     */
    public function assertLastErrorIsFeatureNameIsInvalid(): void
    {
        $this->assertLastErrorIs(
            FeatureConstraintException::class,
            FeatureConstraintException::INVALID_NAME
        );
    }

    /**
     * @Then I should get an error that feature shop association is invalid
     */
    public function assertLastErrorIsInvalidShopAssociation(): void
    {
        $this->assertLastErrorIs(
            FeatureConstraintException::class,
            FeatureConstraintException::INVALID_SHOP_ASSOCIATION
        );
    }

    private function getFeatureForEditing(string $featureReference): EditableFeature
    {
        return $this->getQueryBus()->handle(
            new GetFeatureForEditing($this->getSharedStorage()->get($featureReference))
        );
    }
}
