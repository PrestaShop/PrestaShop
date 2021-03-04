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

namespace PrestaShopBundle\Service\DataProvider\Admin;

/**
 * Data provider for new Architecture, about Module object model.
 *
 * This class will provide data from DB / ORM about Modules for the Admin interface.
 */
interface AddonsInterface
{
    /**
     * @param int $module_id
     *
     * @return bool
     */
    public function downloadModule(int $module_id): bool;

    /**
     * @return bool
     */
    public function isAddonsAuthenticated(): bool;

    /**
     * Check if a request has already failed.
     *
     * @return bool
     */
    public function isAddonsUp(): bool;

    /**
     * Send a request to addons.prestashop.com to retrieve Modules/Addons data.
     *
     * @param string $action the query type
     * @param array $params the request parameters
     */
    public function request($action, $params);
}
