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

//@todo: Im not sure this is the best place for types, but I don't like them being in generic js/types dir.
//    whole js structure needs cleaning (for product) it should be more oriented to feature driven structure rather than type driven
//    e.g. everything related to specificPrices should go to product/specific-price (including components, services(data providers), managers etc.),
//    code related to categories goes to product/category etc.

type SpecificPriceForListing = {
  id: number,
  combination: string,
  currency: string,
  country: string,
  group: string,
  shop: string,
  customer: string,
  price: string,
  impact: string,
  period: Period|null,
  fromQuantity: string,
}

type Period = {
  from: string,
  to: string
}
