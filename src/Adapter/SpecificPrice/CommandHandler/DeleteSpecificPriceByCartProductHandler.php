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

namespace PrestaShop\PrestaShop\Adapter\SpecificPrice\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Command\DeleteSpecificPriceByCartProductCommand;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\CommandHandler\DeleteSpecificPriceByCartProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception\SpecificPriceException;
use PrestaShopException;
use SpecificPrice;

/**
 * Handles DeleteSpecificPriceByCartProduct command using legacy object model
 */
final class DeleteSpecificPriceByCartProductHandler implements DeleteSpecificPriceByCartProductHandlerInterface
{
    /**
     * @param DeleteSpecificPriceByCartProductCommand $command
     *
     * @throws SpecificPriceException
     */
    public function handle(DeleteSpecificPriceByCartProductCommand $command): void
    {
        $productAttributeId = $command->getProductAttributeId() ?? false;
        $cartIdValue = $command->getCartId()->getValue();
        $productIdValue = $command->getProductId()->getValue();

        try {
            if (false === SpecificPrice::deleteByIdCart($cartIdValue, $productIdValue, $productAttributeId)) {
                throw new SpecificPriceException(sprintf('Failed to delete specific price for cart #%s product #%s', $cartIdValue, $productIdValue));
            }
        } catch (PrestaShopException $e) {
            throw new SpecificPriceException(sprintf('An error occurred when trying to delete specific price for cart #%s product #%s', $cartIdValue, $productIdValue));
        }
    }
}
