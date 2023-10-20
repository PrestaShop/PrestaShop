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

namespace PrestaShop\PrestaShop\Adapter\Carrier;

use Db;

/**
 * Provides will modules advice alert show in carriers page.
 */
class CarrierModuleAdviceAlertChecker
{
    /**
     * Should this show modules advice alert on carriers page?
     *
     * @return bool
     */
    public function isAlertDisplayed(): bool
    {
        // If there is carriers that id_reference is higher than 2, there is non-default
        // carriers and then don't show advice.
        $sql = 'SELECT COUNT(1) FROM `' . _DB_PREFIX_ . 'carrier` WHERE deleted = 0 AND id_reference > 2';

        return Db::getInstance()->executeS($sql, false)->fetchColumn(0) == 0;
    }
}
