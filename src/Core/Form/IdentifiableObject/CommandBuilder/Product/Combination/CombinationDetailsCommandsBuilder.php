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

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationDetailsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;

/**
 * Builds commands from command details and impact form type
 */
class CombinationDetailsCommandsBuilder implements CombinationCommandsBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildCommands(CombinationId $combinationId, array $formData): array
    {
        if (!isset($formData['details']) && !isset($formData['price_impact']['weight'])) {
            return [];
        }

        $detailsData = $formData['details'] ?? [];
        $command = new UpdateCombinationDetailsCommand($combinationId->getValue());

        if (isset($detailsData['reference'])) {
            $command->setReference($detailsData['reference']);
        }
        if (isset($detailsData['ean_13'])) {
            $command->setEan13($detailsData['ean_13']);
        }
        if (isset($detailsData['isbn'])) {
            $command->setIsbn($detailsData['isbn']);
        }
        if (isset($detailsData['mpn'])) {
            $command->setMpn($detailsData['mpn']);
        }
        if (isset($detailsData['upc'])) {
            $command->setUpc($detailsData['upc']);
        }

        // This one is special because it is in a different form type
        if (isset($formData['price_impact']['weight'])) {
            $command->setWeight((string) $formData['price_impact']['weight']);
        }

        return [$command];
    }
}
