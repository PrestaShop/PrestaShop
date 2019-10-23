<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\AddCustomizationFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShopException;
use Product;

final class AddCustomizationFieldsHandler extends AbstractCartHandler implements AddCustomizationFieldsHandlerInterface
{
    /**
     * @param AddCustomizationFieldsCommand $command
     *
     * @throws PrestaShopException
     * @throws CartNotFoundException
     */
    public function handle(AddCustomizationFieldsCommand $command)
    {
        //@todo: exceptions handling
        $productId = $command->getProductId()->getValue();

        $cart = $this->getCart($command->getCartId());
        $product = new Product($productId);

        $customizationFields = $product->getCustomizationFieldIds();
        $customizations = $command->getCustomizations();

        foreach ($customizationFields as $customizationField) {
            $customizationId = (int) $customizationField['id_customization_field'];
            //@todo validation
            if (isset($customizations[$customizationId])) {
                if ($customizationField['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                    $cart->addTextFieldToProduct(
                        $productId,
                        $customizationId,
                        Product::CUSTOMIZE_TEXTFIELD,
                        $customizations[$customizationId]
                    );
                    continue;
                }

                //@todo: file validation
                $cart->addPictureToProduct(
                    $productId,
                    $customizationId,
                    Product::CUSTOMIZE_TEXTFIELD,
                    $customizations[$customizationId]
                );
            }
        }
    }
}
