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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductBasicInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Product;

class UpdateBasicInformationFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference basic information for shop :shopReference with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductBasicInfoForShop(string $productReference, string $shopReference, TableNode $table): void
    {
        $shopId = $this->getSharedStorage()->get(trim($shopReference));
        $shopConstraint = ShopConstraint::shop($shopId);
        $this->updateProductBasicInfo($productReference, $table, $shopConstraint);
    }

    /**
     * This method is created just for specific cases when product name needs to be updated
     * using legacy object model, but not cqrs commands, to avoid some side effects while testing.
     * For example when testing how cqrs command auto-fills link_rewrite in certain cases.
     *
     * @When /^I update product "([^"]*)" name \(not using commands\) with following localized values:$/
     *
     * @param string $productReference
     * @param TableNode $table
     *
     * @return void
     */
    public function updateProductName(string $productReference, TableNode $table): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $product = new Product($productId, true);
        $product->name = $this->localizeByRows($table)['name'];

        $product->update();
    }

    /**
     * @When I update product :productReference basic information for all shops with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductBasicInfoForAllShops(string $productReference, TableNode $table): void
    {
        $this->updateProductBasicInfo($productReference, $table, ShopConstraint::allShops());
    }

    /**
     * @When I update product :productReference basic information with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductBasicInfoWithDefaultShop(string $productReference, TableNode $table): void
    {
        $this->updateProductBasicInfo($productReference, $table, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @param string $productReference
     * @param TableNode $table
     * @param ShopConstraint $shopConstraint
     */
    private function updateProductBasicInfo(string $productReference, TableNode $table, ShopConstraint $shopConstraint): void
    {
        $data = $this->localizeByRows($table);
        $productId = $this->getSharedStorage()->get($productReference);
        $command = new UpdateProductBasicInformationCommand($productId, $shopConstraint);

        if (isset($data['name'])) {
            $command->setLocalizedNames($data['name']);
        }

        if (isset($data['description'])) {
            $command->setLocalizedDescriptions($data['description']);
        }

        if (isset($data['description_short'])) {
            $command->setLocalizedShortDescriptions($data['description_short']);
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }
}
