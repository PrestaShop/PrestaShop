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
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductOptionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\UpdateProduct\AbstractUpdateOptionsFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

/**
 * Context for updating product options properties using dedicated UpdateProductOptionsCommand
 *
 * @see UpdateProductOptionsCommand
 */
class UpdateOptionsFeatureContext extends AbstractUpdateOptionsFeatureContext
{
    /**
     * @When I update product :productReference options with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductOptionsForDefaultShop(string $productReference, TableNode $table): void
    {
        $this->updateProductOptions($productReference, $table, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When I update product :productReference options for shop ":shopReference" with following values:
     *
     * @param string $productReference
     * @param string $shopReference
     * @param TableNode $table
     */
    public function updateProductOptionsForShop(string $productReference, string $shopReference, TableNode $table): void
    {
        $shopId = $this->getSharedStorage()->get(trim($shopReference));
        $this->updateProductOptions($productReference, $table, ShopConstraint::shop($shopId));
    }

    /**
     * @When I update product :productReference options for all shops with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductOptionsForAllShops(string $productReference, TableNode $table): void
    {
        $this->updateProductOptions($productReference, $table, ShopConstraint::allShops());
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
            $command = new UpdateProductOptionsCommand(
                $this->getSharedStorage()->get($productReference),
                ShopConstraint::shop($this->getDefaultShopId())
            );
            $command->setManufacturerId($nonExistingId);
            $this->getCommandBus()->handle($command);
        } catch (ManufacturerException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @param string $productReference
     * @param TableNode $table
     * @param ShopConstraint $shopConstraint
     */
    private function updateProductOptions(string $productReference, TableNode $table, ShopConstraint $shopConstraint): void
    {
        $data = $table->getRowsHash();
        $productId = $this->getSharedStorage()->get($productReference);

        try {
            $command = new UpdateProductOptionsCommand($productId, $shopConstraint);
            $this->fillCommand($data, $command);
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @param array $data
     * @param UpdateProductOptionsCommand $command
     */
    private function fillCommand(array $data, UpdateProductOptionsCommand $command): void
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

        if (isset($data['show_condition'])) {
            $command->setShowCondition(PrimitiveUtils::castStringBooleanIntoBoolean($data['show_condition']));
        }

        if (isset($data['manufacturer'])) {
            $command->setManufacturerId($this->getManufacturerId($data['manufacturer']));
        }
    }
}
