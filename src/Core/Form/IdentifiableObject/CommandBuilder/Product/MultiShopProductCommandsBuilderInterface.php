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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * This interface is similar to ProductCommandsBuilderInterface except it handles the product commands
 * which are related to multishop fields, so it has an extra $singleShopConstraint parameter.
 *
 * @todo: since not all builders are migrated yet we need two interfaces but in the this is the only
 *        one that should remain, so it will be merged back or renamed as the initial one ProductCommandsBuilderInterface
 *        and the shop constraint parameter will always be mandatory (there might a few builders which won't need it
 *        though, but it doesn't matter) So this interface is a temporary one just like @see ProductMultiShopRepository
 */
interface MultiShopProductCommandsBuilderInterface
{
    /**
     * @param ProductId $productId
     * @param array $formData
     * @param ShopConstraint $singleShopConstraint
     *
     * @return array Returns empty array if the required data for the command is absent
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array;
}
