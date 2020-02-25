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
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationException;
use PrestaShopException;
use Product;

/**
 * Adds product customization fields data using legacy object model
 */
final class AddCustomizationFieldsHandler extends AbstractCartHandler implements AddCustomizationFieldsHandlerInterface
{
    /**
     * @param AddCustomizationFieldsCommand $command
     *
     * @return int
     *
     * @throws CartNotFoundException
     * @throws CustomizationConstraintException
     * @throws CustomizationException
     * @throws PrestaShopException
     */
    public function handle(AddCustomizationFieldsCommand $command): int
    {
        $productId = $command->getProductId()->getValue();

        $cart = $this->getCart($command->getCartId());
        $product = new Product($productId);

        $customizationFields = $product->getCustomizationFieldIds();
        $customizations = $command->getCustomizationsByFieldIds();

        foreach ($customizationFields as $customizationField) {
            $customizationFieldId = (int) $customizationField['id_customization_field'];
            //@todo validation
            if (!isset($customizations[$customizationFieldId])) {
                continue;
            }

            try {
                if (Product::CUSTOMIZE_TEXTFIELD == $customizationField['type']) {
                    $customizationId = $cart->addTextFieldToProduct(
                        $productId,
                        $customizationFieldId,
                        Product::CUSTOMIZE_TEXTFIELD,
                        $customizations[$customizationFieldId],
                        true
                    );
                } else {
                    //@todo: file validation
                    $customizationId = $cart->addPictureToProduct(
                        $productId,
                        $customizationFieldId,
                        Product::CUSTOMIZE_TEXTFIELD,
                        $customizations[$customizationFieldId],
                        true
                    );
                }

                if (false === $customizationId) {
                    throw new CustomizationException(sprintf(
                        'Failed to add customized data for customization field with id "%s"',
                        $customizationFieldId
                    ));
                }
            } catch (PrestaShopException $e) {
                throw new CustomizationException(sprintf(
                    'An error occurred while trying to add customized data for customization field with id "%s"',
                    $customizationFieldId
                ));
            }
        }

        if (!isset($customizationId)) {
            throw new CustomizationConstraintException(
                'Invalid customizations provided.
                It must consist of key - value pairs where key is the id of customization field'
            );
        }

        return $customizationId;
    }
}
