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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductInput\BasicInformationInput;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductInput\ProductOptionsInput;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

//@todo: abit confusing when it should always build only one command but is called plural and returns array. smth needs adjustments
class UpdateProductCommandsBuilder implements MultiShopProductCommandsBuilderInterface
{
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $shopConstraint): array
    {
        $updateProductCommand = new UpdateProductCommand($productId, $shopConstraint);

        $updateProductCommand
            ->setBasicInformation($this->buildBasicInfo($formData))
            ->setOptions($this->buildOptions($formData))
        ;

        return [$updateProductCommand];
    }

    private function buildBasicInfo(array $formData): ?BasicInformationInput
    {
        if (empty($formData['description']) && empty($formData['header']['name'])) {
            return null;
        }

        //@todo: command builders is almost what we could use to build these dto's but not quite. Maybe its ok this simple way?
        $basicInformationInput = new BasicInformationInput();
        $basicInformationInput
            ->setLocalizedNames($formData['header']['name'])
            ->setLocalizedDescriptions($formData['description']['description'])
            ->setLocalizedShortDescriptions($formData['description']['description_short'])
        ;

        return $basicInformationInput;
    }

    private function buildOptions(array $formData): ?ProductOptionsInput
    {
        if (empty($formData['options']) &&
            !isset($formData['description']['manufacturer']) &&
            !isset($formData['specifications'])) {
            return null;
        }

        $productOptionsInput = new ProductOptionsInput();
        $productOptionsInput
            ->setManufacturerId((int) $formData['description']['manufacturer'])
            ->setOnlineOnly((bool) $formData['options']['visibility']['online_only'])
            ->setVisibility($formData['options']['visibility']['visibility'])
            ->setAvailableForOrder((bool) $formData['options']['visibility']['available_for_order'])
            ->setShowPrice((bool) $formData['options']['visibility']['show_price'])
            ->setShowCondition((bool) $formData['specifications']['show_condition'])
        ;

        // based on show_condition value, the condition field can be disabled, in that case "condition" won't exist in request
        if (!empty($formData['specifications']['condition'])) {
            $productOptionsInput->setCondition((string) $formData['specifications']['condition']);
        }

        return $productOptionsInput;
    }
}
