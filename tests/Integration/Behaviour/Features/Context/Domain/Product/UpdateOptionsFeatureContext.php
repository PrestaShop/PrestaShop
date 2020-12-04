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
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
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
        } catch (ManufacturerException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get error that assigned manufacturer is invalid
     */
    public function assertInvalidManufacturerError(): void
    {
        $this->assertLastErrorIs(
            ManufacturerConstraintException::class,
            ManufacturerConstraintException::INVALID_ID
        );
    }

    /**
     * @Then I should get error that assigned manufacturer does not exist
     */
    public function assertManufacturerDoesNotExistError(): void
    {
        $this->assertLastErrorIs(
            ManufacturerNotFoundException::class
        );
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
            $this->parseManufacturerId($dataRows['manufacturer'])
        );
    }

    /**
     * @Then product :productReference should have following options:
     *
     * @param string $productReference
     * @param ProductOptions $expectedOptions
     */
    public function assertOptions(string $productReference, ProductOptions $expectedOptions)
    {
        $properties = [
            'active',
            'availableForOrder',
            'onlineOnly',
            'showPrice',
            'visibility',
            'condition',
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
     * @Then manufacturer :manufacturerReference should be assigned to product :productReference
     *
     * @param string $manufacturerReference
     * @param string $productReference
     */
    public function assertManufacturerId(string $manufacturerReference, string $productReference): void
    {
        $expectedId = $this->getSharedStorage()->get($manufacturerReference);
        $actualId = $this->getProductForEditing($productReference)->getOptions()->getManufacturerId();

        Assert::assertEquals($expectedId, $actualId, 'Unexpected product manufacturer id');
    }

    /**
     * @Then product :productReference should have no manufacturer assigned
     *
     * @param string $productReference
     */
    public function assertProductHasNoManufacturer(string $productReference): void
    {
        $manufacturerId = $this->getProductForEditing($productReference)->getOptions()->getManufacturerId();
        Assert::assertEmpty($manufacturerId, sprintf('Expected product "%s" to have no manufacturer assigned', $productReference));
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

        if (isset($data['manufacturer'])) {
            $command->setManufacturerId($this->parseManufacturerId($data['manufacturer']));
        }
    }

    /**
     * @param string $value
     *
     * @return int
     */
    private function parseManufacturerId(string $value): int
    {
        switch ($value) {
            case 'invalid':
                $manufacturerId = -1;
                break;
            case 'non-existent':
                $manufacturerId = 42;
                break;
            case '':
                $manufacturerId = NoManufacturerId::NO_MANUFACTURER_ID;
                break;
            default:
                $manufacturerId = $this->getSharedStorage()->get($value);
                break;
        }

        return $manufacturerId;
    }
}
