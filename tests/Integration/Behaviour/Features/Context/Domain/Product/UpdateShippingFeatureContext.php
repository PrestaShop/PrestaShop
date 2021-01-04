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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use Carrier;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductShippingCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductShippingInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class UpdateShippingFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference shipping information with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductShipping(string $productReference, TableNode $table): void
    {
        $data = $this->localizeByRows($table);
        $productId = $this->getSharedStorage()->get($productReference);

        try {
            $command = new UpdateProductShippingCommand($productId);
            $unhandledData = $this->setUpdateShippingCommandData($data, $command);

            Assert::assertEmpty(
                $unhandledData,
                sprintf('Not all provided values handled in scenario. %s', var_export($unhandledData, true))
            );

            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then product :productReference should have no carriers assigned
     *
     * @param string $productReference
     */
    public function assertProductHasNoCarriers(string $productReference): void
    {
        $productForEditing = $this->getProductForEditing($productReference);

        Assert::assertEmpty(
            $productForEditing->getShippingInformation()->getCarrierReferences(),
            sprintf('Expected product "%s" to have no carriers assigned', $productReference)
        );
    }

    /**
     * @Then product :productReference should have following shipping information:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function assertShippingInformation(string $productReference, TableNode $tableNode): void
    {
        $data = $this->localizeByRows($tableNode);
        $productShippingInformation = $this->getProductForEditing($productReference)->getShippingInformation();

        if (isset($data['carriers'])) {
            $expectedReferenceIds = $this->getCarrierReferenceIds($data['carriers']);
            $actualReferenceIds = $productShippingInformation->getCarrierReferences();

            Assert::assertEquals(
                $expectedReferenceIds,
                $actualReferenceIds,
                'Unexpected carrier references in product shipping information'
            );

            unset($data['carriers']);
        }

        $this->assertNumberShippingFields($data, $productShippingInformation);
        $this->assertDeliveryTimeNotes($data, $productShippingInformation);

        // Assertions checking isset() can hide some errors if it doesn't find array key,
        // to make sure all provided fields were checked we need to unset every asserted field
        // and finally, if provided data is not empty, it means there are some unnasserted values left
        Assert::assertEmpty($data, sprintf('Some provided product shipping fields haven\'t been asserted: %s', var_export($data, true)));
    }

    /**
     * @param array $expectedValues
     * @param ProductShippingInformation $actualValues
     */
    private function assertNumberShippingFields(array &$expectedValues, ProductShippingInformation $actualValues): void
    {
        $numberShippingFields = [
            'width',
            'height',
            'depth',
            'weight',
            'additional_shipping_cost',
        ];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($numberShippingFields as $field) {
            if (isset($expectedValues[$field])) {
                $expectedNumber = new DecimalNumber((string) $expectedValues[$field]);
                $actualNumber = $propertyAccessor->getValue($actualValues, $field);

                if (!$expectedNumber->equals($actualNumber)) {
                    throw new RuntimeException(
                        sprintf('Product %s expected to be "%s", but is "%s"', $field, $expectedNumber, $actualNumber)
                    );
                }

                unset($expectedValues[$field]);
            }
        }
    }

    /**
     * @param array $data
     * @param ProductShippingInformation $productShippingInformation
     */
    private function assertDeliveryTimeNotes(array &$data, ProductShippingInformation $productShippingInformation): void
    {
        $notesTypeNamedValues = [
            'none' => DeliveryTimeNoteType::TYPE_NONE,
            'default' => DeliveryTimeNoteType::TYPE_DEFAULT,
            'specific' => DeliveryTimeNoteType::TYPE_SPECIFIC,
        ];

        if (isset($data['delivery time notes type'])) {
            $expectedType = $notesTypeNamedValues[$data['delivery time notes type']];
            $actualType = $productShippingInformation->getDeliveryTimeNoteType();
            Assert::assertEquals($expectedType, $actualType, 'Unexpected delivery time notes type value');

            unset($data['delivery time notes type']);
        }

        if (isset($data['delivery time in stock notes'])) {
            $actualLocalizedOutOfStockNotes = $productShippingInformation->getLocalizedDeliveryTimeInStockNotes();
            Assert::assertEquals(
                $data['delivery time in stock notes'],
                $actualLocalizedOutOfStockNotes,
                'Unexpected product delivery time in stock notes'
            );

            unset($data['delivery time in stock notes']);
        }

        if (isset($data['delivery time out of stock notes'])) {
            $actualLocalizedOutOfStockNotes = $productShippingInformation->getLocalizedDeliveryTimeOutOfStockNotes();
            Assert::assertEquals(
                $data['delivery time out of stock notes'],
                $actualLocalizedOutOfStockNotes,
                'Unexpected product delivery time out of stock notes'
            );

            unset($data['delivery time out of stock notes']);
        }
    }

    /**
     * @param array $data
     * @param UpdateProductShippingCommand $command
     *
     * @return array values that was provided, but wasn't handled
     */
    private function setUpdateShippingCommandData(array $data, UpdateProductShippingCommand $command): array
    {
        $unhandledValues = $data;

        if (isset($data['width'])) {
            $command->setWidth($data['width']);
            unset($unhandledValues['width']);
        }

        if (isset($data['height'])) {
            $command->setHeight($data['height']);
            unset($unhandledValues['height']);
        }

        if (isset($data['depth'])) {
            $command->setDepth($data['depth']);
            unset($unhandledValues['depth']);
        }

        if (isset($data['weight'])) {
            $command->setWeight($data['weight']);
            unset($unhandledValues['weight']);
        }

        if (isset($data['additional_shipping_cost'])) {
            $command->setAdditionalShippingCost($data['additional_shipping_cost']);
            unset($unhandledValues['additional_shipping_cost']);
        }

        if (isset($data['delivery time notes type'])) {
            $command->setDeliveryTimeNoteType(DeliveryTimeNoteType::ALLOWED_TYPES[$data['delivery time notes type']]);
            unset($unhandledValues['delivery time notes type']);
        }

        if (isset($data['delivery time in stock notes'])) {
            $command->setLocalizedDeliveryTimeInStockNotes($data['delivery time in stock notes']);
            unset($unhandledValues['delivery time in stock notes']);
        }

        if (isset($data['delivery time out of stock notes'])) {
            $command->setLocalizedDeliveryTimeOutOfStockNotes($data['delivery time out of stock notes']);
            unset($unhandledValues['delivery time out of stock notes']);
        }

        if (isset($data['carriers'])) {
            $command->setCarrierReferences($this->getCarrierReferenceIds($data['carriers']));
            unset($unhandledValues['carriers']);
        }

        return $unhandledValues;
    }

    /**
     * @param string $carrierReferencesInput
     *
     * @return int[]
     */
    private function getCarrierReferenceIds(string $carrierReferencesInput): array
    {
        $referenceIds = [];
        foreach (PrimitiveUtils::castStringArrayIntoArray($carrierReferencesInput) as $carrierReference) {
            $carrier = new Carrier($this->getSharedStorage()->get($carrierReference));
            $referenceIds[] = (int) $carrier->id_reference;
        }

        return $referenceIds;
    }
}
