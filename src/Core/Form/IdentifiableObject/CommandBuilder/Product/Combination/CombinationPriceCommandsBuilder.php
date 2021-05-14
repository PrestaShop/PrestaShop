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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;

/**
 * Builds commands from command price form type
 */
class CombinationPriceCommandsBuilder implements CombinationCommandsBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommands(CombinationId $combinationId, array $formData): array
    {
        if (!isset($formData['price_impact'])) {
            return [];
        }

        $quantityData = $formData['price_impact'];
        $command = new UpdateCombinationPricesCommand($combinationId->getValue());

        if (isset($quantityData['price_tax_excluded'])) {
            $command->setImpactOnPrice((string) $quantityData['price_tax_excluded']);
        }
        if (isset($quantityData['ecotax'])) {
            $command->setEcoTax((string) $quantityData['ecotax']);
        }
        if (isset($quantityData['wholesale_price'])) {
            $command->setWholesalePrice((string) $quantityData['wholesale_price']);
        }
        if (isset($quantityData['unit_price'])) {
            $command->setImpactOnUnitPrice((string) $quantityData['unit_price']);
        }

        return [$command];
    }
}
