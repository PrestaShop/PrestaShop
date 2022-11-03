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
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractProductFeatureContext;

/**
 * Context for product assertions related to Details related properties
 */
class UpdateDetailsFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference details with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductDetails(string $productReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        try {
            $command = new UpdateProductCommand(
                $this->getSharedStorage()->get($productReference),
                ShopConstraint::shop($this->getDefaultShopId())
            );
            $this->fillCommand($data, $command);
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
        } catch (ManufacturerException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param UpdateProductCommand $command
     */
    private function fillCommand(array $data, UpdateProductCommand $command): void
    {
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
    }
}
