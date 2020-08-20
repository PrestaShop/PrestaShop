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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductOptionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class UpdateOptionsFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference options with following information:
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
            $this->setUpdateOptionsCommandData($data, $command);
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
     * @param array $data
     * @param UpdateProductOptionsCommand $command
     */
    private function setUpdateOptionsCommandData(array $data, UpdateProductOptionsCommand $command): void
    {
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

        if (isset($data['isbn'])) {
            $command->setIsbn($data['isbn']);
        }

        if (isset($data['upc'])) {
            $command->setUpc($data['upc']);
        }

        if (isset($data['ean13'])) {
            $command->setEan13($data['ean13']);
        }

        if (isset($data['mpn'])) {
            $command->setMpn($data['mpn']);
        }

        if (isset($data['reference'])) {
            $command->setReference($data['reference']);
        }

        if (isset($data['mpn'])) {
            $command->setMpn($data['mpn']);
        }

        if (isset($data['manufacturer'])) {
            switch ($data['manufacturer']) {
                case 'invalid':
                    $manufacturerId = -1;
                    break;
                case 'non-existent':
                    $manufacturerId = 42;
                    break;
                case '':
                    $manufacturerId = 0;
                    break;
                default:
                    $manufacturerId = $this->getSharedStorage()->get($data['manufacturer']);
                    break;
            }
            $command->setManufacturerId($manufacturerId);
        }
    }

    /**
     * @Then product :productReference should have following options information:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertOptionsInformation(string $productReference, TableNode $table)
    {
        $productForEditing = $this->getProductForEditing($productReference);
        $data = $table->getRowsHash();

        $this->assertBoolProperty($productForEditing, $data, 'available_for_order');
        $this->assertBoolProperty($productForEditing, $data, 'online_only');
        $this->assertBoolProperty($productForEditing, $data, 'show_price');
        $this->assertStringProperty($productForEditing, $data, 'visibility');
        $this->assertStringProperty($productForEditing, $data, 'condition');
        $this->assertStringProperty($productForEditing, $data, 'isbn');
        $this->assertStringProperty($productForEditing, $data, 'upc');
        $this->assertStringProperty($productForEditing, $data, 'ean13');
        $this->assertStringProperty($productForEditing, $data, 'mpn');
        $this->assertStringProperty($productForEditing, $data, 'reference');

        // Assertions checking isset() can hide some errors if it doesn't find array key,
        // to make sure all provided fields were checked we need to unset every asserted field
        // and finally, if provided data is not empty, it means there are some unnasserted values left
        Assert::assertEmpty($data, sprintf('Some provided product options fields haven\'t been asserted: %s', var_export($data, true)));
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
}
