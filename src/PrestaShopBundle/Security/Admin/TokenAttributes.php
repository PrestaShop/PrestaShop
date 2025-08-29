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

namespace PrestaShopBundle\Security\Admin;

class TokenAttributes
{
    /**
     * Used to store the IP address as an integer to compare if it changed (to avoid copying/stealing the cookie session)
     */
    public const IP_ADDRESS = '_ip_address';

    /**
     * Used to store the serialized EmployeeSession object which is then used to check if is still valid (in the DB).
     */
    public const EMPLOYEE_SESSION = '_employee_session';

    /**
     * Used to store a ShopConstraint object that represents the current Shop context (All shops, single shop, shop group).
     */
    public const SHOP_CONSTRAINT = '_shop_constraint';
}
