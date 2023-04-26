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

namespace PrestaShopBundle\Model;

/**
 * @deprecated since 8.1 and will be removed in next major.
 *
 * This form class is responsible to map the form data to the posted object.
 *
 * For this parent class, only hook sub fields are handled.
 */
class AdminModelAdapter
{
    /**
     * modelMapper
     * Map form data to object model.
     *
     * This parent method will return only hook sub array.
     *
     * @return array Transformed form data to model attempt
     */
    public function getHookData()
    {
        // Hook fields are kept.
        if (array_key_exists('hook', $_POST)) {
            $hookFields = $_POST['hook'];

            return ['hook' => $hookFields];
        }

        return [];
    }
}
