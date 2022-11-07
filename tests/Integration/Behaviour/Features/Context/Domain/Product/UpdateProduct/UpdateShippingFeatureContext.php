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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\UpdateProduct;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class UpdateShippingFeatureContext extends AbstractShippingFeatureContext
{
    /**
     * @When I update product :productReference shipping information with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductShipping(string $productReference, TableNode $table): void
    {
        $this->updateShipping($productReference, $table, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When I update product :productReference shipping information for shop :shopReference with following values:
     *
     * @param string $productReference
     * @param string $shopReference
     * @param TableNode $table
     */
    public function updateProductShippingForShop(string $productReference, string $shopReference, TableNode $table): void
    {
        $shopId = $this->getSharedStorage()->get(trim($shopReference));
        $this->updateShipping($productReference, $table, ShopConstraint::shop($shopId));
    }

    /**
     * @When I update product :productReference shipping information for all shops with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductShippingForAllShops(string $productReference, TableNode $table): void
    {
        $this->updateShipping($productReference, $table, ShopConstraint::allShops());
    }

    /**
     * @param string $productReference
     * @param TableNode $table
     * @param ShopConstraint $shopConstraint
     */
    private function updateShipping(string $productReference, TableNode $table, ShopConstraint $shopConstraint): void
    {
        $data = $this->localizeByRows($table);
        $productId = $this->getSharedStorage()->get($productReference);

        try {
            $command = new UpdateProductCommand($productId, $shopConstraint);
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
     * @param array $data
     * @param UpdateProductCommand $command
     *
     * @return array values that was provided, but wasn't handled
     */
    private function setUpdateShippingCommandData(array $data, UpdateProductCommand $command): array
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
            $command->setCarrierReferenceIds($this->getCarrierReferenceIds($data['carriers']));
            unset($unhandledValues['carriers']);
        }

        return $unhandledValues;
    }
}
