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

/**
 * Builder used to build UpdateProductPricesCommand
 */
class PricesCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData): array
    {
        if (!isset($formData['pricing'])) {
            return [];
        }

        $priceData = $formData['pricing'];
        $command = new UpdateProductPricesCommand($productId->getValue());

        if (isset($priceData['retail_price']['price_tax_excluded'])) {
            $command->setPrice((string) $priceData['retail_price']['price_tax_excluded']);
        }
        if (isset($priceData['retail_price']['ecotax'])) {
            $command->setEcotax((string) $priceData['retail_price']['ecotax']);
        }
        if (isset($priceData['tax_rules_group_id'])) {
            $command->setTaxRulesGroupId((int) $priceData['tax_rules_group_id']);
        }
        if (isset($priceData['on_sale'])) {
            $command->setOnSale((bool) $priceData['on_sale']);
        }
        if (isset($priceData['wholesale_price'])) {
            $command->setWholesalePrice((string) $priceData['wholesale_price']);
        }
        if (isset($priceData['unit_price']['price'])) {
            $command->setUnitPrice((string) $priceData['unit_price']['price']);
        }
        if (isset($priceData['unit_price']['unity'])) {
            $command->setUnity((string) $priceData['unit_price']['unity']);
        }

        return [$command];
    }
}
