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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization;

use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * The customization field commands/queries only handles single shop use case, we didn't implement the allShops use case because
 * it was considered too complex to handle compared to the benefit. The "apply to all shops" boolean can't be assigned on the
 * command itself but on each contained CustomizationField in the command. Besides the association of customization fields is not
 * dependent to a specific shop they are common to all shops, and it only allows update the name field for a specific shop.
 *
 * A POC had been started in case the feature needs to evolve someday https://github.com/PrestaShop/PrestaShop/pull/27944
 */
trait CustomizationShopConstraintTrait
{
    /**
     * @param ShopConstraint $shopConstraint
     *
     * @throws InvalidShopConstraintException
     */
    protected function checkShopConstraint(ShopConstraint $shopConstraint): void
    {
        if ($shopConstraint->forAllShops() || $shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException(sprintf(
                '%s only handles single shop constraint.',
                self::class
            ));
        }
    }
}
