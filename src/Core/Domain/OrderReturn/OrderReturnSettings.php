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

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn;

class OrderReturnSettings
{
    /**
     * Order return status ID's are hardcoded here because they are also hardcoded during the installation.
     * In Legacy plain ID's where used, they should all be replaced with constants here.
     * In case new Order Return status is added as part of update to the project then it should be saved in configuration table.
     * In that case this part should be refactored so ID's here also stored in configuration table for consistency.
     */
    public const ORDER_RETURN_STATE_WAITING_FOR_CONFIRMATION = 1;
    public const ORDER_RETURN_STATE_WAITING_FOR_PACKAGE_ID = 2;
    public const ORDER_RETURN_STATE_PACKAGE_RECEIVED = 3;
    public const ORDER_RETURN_STATE_RETURN_DENIED = 4;
    public const ORDER_RETURN_STATE_RETURN_COMPLETED = 5;
}
