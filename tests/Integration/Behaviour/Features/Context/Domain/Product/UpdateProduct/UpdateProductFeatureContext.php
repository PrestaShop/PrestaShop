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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractProductFeatureContext;

class UpdateProductFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference for shop :shopReference with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductForShop(string $productReference, string $shopReference, TableNode $table): void
    {
        $shopId = $this->getSharedStorage()->get(trim($shopReference));
        $shopConstraint = ShopConstraint::shop($shopId);
        $this->updateProduct($productReference, $table, $shopConstraint);
    }

    /**
     * @param string $productReference
     * @param TableNode $table
     * @param ShopConstraint $shopConstraint
     */
    private function updateProduct(string $productReference, TableNode $table, ShopConstraint $shopConstraint): void
    {
        $command = $this->buildUpdateProductCommand($productReference, $table, $shopConstraint);

        try {
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @param string $productReference
     * @param TableNode $table
     * @param ShopConstraint $shopConstraint
     *
     * @return UpdateProductCommand
     */
    private function buildUpdateProductCommand(string $productReference, TableNode $table, ShopConstraint $shopConstraint): UpdateProductCommand
    {
        $data = $this->localizeByRows($table);
        $productId = $this->getSharedStorage()->get($productReference);
        $command = new UpdateProductCommand($productId, $shopConstraint);

        if (isset($data['name'])) {
            $command->setLocalizedNames($data['name']);
        }

        if (isset($data['description'])) {
            $command->setLocalizedDescriptions($data['description']);
        }

        if (isset($data['description_short'])) {
            $command->setLocalizedShortDescriptions($data['description_short']);
        }

        return $command;
    }
}
