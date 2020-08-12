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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductBasicInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class UpdateBasicInformationFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference basic information with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductBasicInfo(string $productReference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $productId = $this->getSharedStorage()->get($productReference);
        $command = new UpdateProductBasicInformationCommand($productId);

        if (isset($data['name'])) {
            $command->setLocalizedNames($this->parseLocalizedArray($data['name']));
        }

        if (isset($data['is_virtual'])) {
            $command->setVirtual(PrimitiveUtils::castStringBooleanIntoBoolean($data['is_virtual']));
        }

        if (isset($data['description'])) {
            $command->setLocalizedDescriptions($this->parseLocalizedArray($data['description']));
        }

        if (isset($data['description_short'])) {
            $command->setLocalizedShortDescriptions($this->parseLocalizedArray($data['description_short']));
        }

        if (isset($data['manufacturer'])) {
            $command->setManufacturerId($this->getSharedStorage()->get($data['manufacturer']));
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
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
        $actualId = $this->getProductForEditing($productReference)->getBasicInformation()->getManufacturerId();

        Assert::assertEquals($expectedId, $actualId, 'Unexpected product manufacturer id');
    }

    /**
     * @Then product :productReference should have no manufacturer assigned
     *
     * @param string $productReference
     */
    public function assertProductHasNoManufacturer(string $productReference): void
    {
        $manufacturerId = $this->getProductForEditing($productReference)->getBasicInformation()->getManufacturerId();
        Assert::assertEmpty($manufacturerId, sprintf('Expected product "%s" to have no manufacturer assigned', $productReference));
    }
}
