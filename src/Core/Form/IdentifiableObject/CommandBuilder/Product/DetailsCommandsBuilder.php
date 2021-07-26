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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductDetailsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class DetailsCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * @param ProductId $productId
     * @param array $formData
     *
     * @return array|UpdateProductDetailsCommand[]
     */
    public function buildCommands(ProductId $productId, array $formData): array
    {
        if (empty($formData['specifications']['references'])) {
            return [];
        }

        $referencesData = $formData['specifications']['references'];
        $command = new UpdateProductDetailsCommand($productId->getValue());

        if (isset($referencesData['reference'])) {
            $command->setReference($referencesData['reference']);
        }
        if (isset($referencesData['mpn'])) {
            $command->setMpn($referencesData['mpn']);
        }
        if (isset($referencesData['upc'])) {
            $command->setUpc($referencesData['upc']);
        }
        if (isset($referencesData['ean_13'])) {
            $command->setEan13($referencesData['ean_13']);
        }
        if (isset($referencesData['isbn'])) {
            $command->setIsbn($referencesData['isbn']);
        }

        return [$command];
    }
}
