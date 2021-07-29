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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Builder used to build UpdateProductPricesCommand
 */
class PricesCommandsBuilder extends MultistoreCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * @var UpdateProductPricesCommand
     */
    private $singleShopCommand;

    /**
     * @var UpdateProductPricesCommand
     */
    private $allShopsCommand;

    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData): array
    {
        if (!isset($formData['pricing'])) {
            return [];
        }

        $priceData = $formData['pricing'];

        if (isset($priceData['retail_price']['price_tax_excluded'])) {
            if (!empty($priceData['retail_price']['multistore_override_all_price_tax_excluded'])) {
                $this->getAllShopsCommand($productId)->setPrice((string) $priceData['retail_price']['price_tax_excluded']);
            } else {
                $this->getSingleCommand($productId)->setPrice((string) $priceData['retail_price']['price_tax_excluded']);
            }
        }
        if (isset($priceData['retail_price']['ecotax'])) {
            $this->getSingleCommand($productId)->setEcotax((string) $priceData['retail_price']['ecotax']);
        }
        if (isset($priceData['tax_rules_group_id'])) {
            $this->getSingleCommand($productId)->setTaxRulesGroupId((int) $priceData['tax_rules_group_id']);
        }
        if (isset($priceData['on_sale'])) {
            $this->getSingleCommand($productId)->setOnSale((bool) $priceData['on_sale']);
        }
        if (isset($priceData['wholesale_price'])) {
            $this->getSingleCommand($productId)->setWholesalePrice((string) $priceData['wholesale_price']);
        }
        if (isset($priceData['unit_price']['price_tax_excluded'])) {
            $this->getSingleCommand($productId)->setUnitPrice((string) $priceData['unit_price']['price_tax_excluded']);
        }
        if (isset($priceData['unit_price']['unity'])) {
            $this->getSingleCommand($productId)->setUnity((string) $priceData['unit_price']['unity']);
        }

        return $this->getCommands();
    }

    /**
     * @return UpdateProductPricesCommand[]
     */
    private function getCommands(): array
    {
        $commands = [];
        if (null !== $this->singleShopCommand) {
            $commands[] = $this->singleShopCommand;
        }
        if (null !== $this->allShopsCommand) {
            $commands[] = $this->allShopsCommand;
        }

        return $commands;
    }

    /**
     * @param ProductId $productId
     *
     * @return UpdateProductPricesCommand
     */
    private function getSingleCommand(ProductId $productId): UpdateProductPricesCommand
    {
        if (null === $this->singleShopCommand) {
            $this->singleShopCommand = new UpdateProductPricesCommand($productId->getValue(), $this->getShopConstraint());
        }

        return $this->singleShopCommand;
    }

    /**
     * @param ProductId $productId
     *
     * @return UpdateProductPricesCommand
     */
    private function getAllShopsCommand(ProductId $productId): UpdateProductPricesCommand
    {
        if (null === $this->allShopsCommand) {
            $this->allShopsCommand = new UpdateProductPricesCommand($productId->getValue(), ShopConstraint::allShops());
        }

        return $this->allShopsCommand;
    }
}
