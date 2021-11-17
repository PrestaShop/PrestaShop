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
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\NoManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductOptionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductOptions;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class UpdateOptionsFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference options with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductOptions(string $productReference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $productId = $this->getSharedStorage()->get($productReference);

        try {
            $command = new UpdateProductOptionsCommand($productId);
            $this->fillCommand($data, $command);
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I assign non existing manufacturer to product :productReference
     *
     * @param string $productReference
     */
    public function updateOptionsWithNonExistingManufacturer(string $productReference): void
    {
        // intentional. Mimics id of non-existing manufacturer
        $nonExistingId = 50000;

        try {
            $command = new UpdateProductOptionsCommand($this->getSharedStorage()->get($productReference));
            $command->setManufacturerId($nonExistingId);
            $this->getCommandBus()->handle($command);
        } catch (ManufacturerException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Transform table:product option,value
     *
     * @param TableNode $tableNode
     *
     * @return ProductOptions
     */
    public function transformOptions(TableNode $tableNode): ProductOptions
    {
        $dataRows = $tableNode->getRowsHash();

        return new ProductOptions(
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['active']),
            $dataRows['visibility'],
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['available_for_order']),
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['online_only']),
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['show_price']),
            $dataRows['condition'],
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['show_condition']),
            $this->getManufacturerId($dataRows['manufacturer'])
        );
    }

    /**
     * @Then product :productReference should have following options:
     *
     * @param string $productReference
     * @param ProductOptions $expectedOptions
     */
    public function assertOptions(string $productReference, ProductOptions $expectedOptions): void
    {
        $properties = [
            'active',
            'availableForOrder',
            'onlineOnly',
            'showPrice',
            'visibility',
            'condition',
            'showCondition',
            'manufacturerId',
        ];
        $actualOptions = $this->getProductForEditing($productReference)->getOptions();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($properties as $property) {
            Assert::assertSame(
                $propertyAccessor->getValue($expectedOptions, $property),
                $propertyAccessor->getValue($actualOptions, $property),
                sprintf('Unexpected %s of product "%s"', $property, $productReference)
            );
        }
    }

    /**
     * @param array $data
     * @param UpdateProductOptionsCommand $command
     */
    private function fillCommand(array $data, UpdateProductOptionsCommand $command): void
    {
        if (isset($data['active'])) {
            $command->setActive(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']));
        }

        if (isset($data['visibility'])) {
            $command->setVisibility($data['visibility']);
        }

        if (isset($data['available_for_order'])) {
            $command->setAvailableForOrder(PrimitiveUtils::castStringBooleanIntoBoolean($data['available_for_order']));
        }

        if (isset($data['online_only'])) {
            $command->setOnlineOnly(PrimitiveUtils::castStringBooleanIntoBoolean($data['online_only']));
        }

        if (isset($data['show_price'])) {
            $command->setShowPrice(PrimitiveUtils::castStringBooleanIntoBoolean($data['show_price']));
        }

        if (isset($data['condition'])) {
            $command->setCondition($data['condition']);
        }

        if (isset($data['show_condition'])) {
            $command->setShowCondition(PrimitiveUtils::castStringBooleanIntoBoolean($data['show_condition']));
        }

        if (isset($data['manufacturer'])) {
            $command->setManufacturerId($this->getManufacturerId($data['manufacturer']));
        }
    }

    /**
     * @param string $manufacturerReference
     *
     * @return int
     */
    private function getManufacturerId(string $manufacturerReference): int
    {
        if ('' === $manufacturerReference) {
            return NoManufacturerId::NO_MANUFACTURER_ID;
        }

        return $this->getSharedStorage()->get($manufacturerReference);
    }
}
