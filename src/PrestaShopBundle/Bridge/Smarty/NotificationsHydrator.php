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

namespace PrestaShopBundle\Bridge\Smarty;

use \Configuration;
use \Media;
use PrestaShopBundle\Bridge\Controller\ControllerConfiguration;
use \Profile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class NotificationsHydrator hydrate notifications in the ControllerConfiguration
 */
class NotificationsHydrator implements HydratorInterface
{
    /**
     * Sets the smarty variables and js defs used to show / hide some notifications.
     *
     * @param ControllerConfiguration $controllerConfiguration
     *
     * @return void
     */
    public function hydrate(ControllerConfiguration $controllerConfiguration)
    {
        $accesses = Profile::getProfileAccesses($controllerConfiguration->user->getData()->id_profile, 'class_name');

        $notificationsSettings = [
            'show_new_customers' => Configuration::get('PS_SHOW_NEW_CUSTOMERS') && isset($accesses['AdminCustomers']) && $accesses['AdminCustomers']['view'] ? '1' : false,
            'show_new_messages' => Configuration::get('PS_SHOW_NEW_MESSAGES') && isset($accesses['AdminCustomerThreads']) && $accesses['AdminCustomerThreads']['view'] ? '1' : false,
            'show_new_orders' => Configuration::get('PS_SHOW_NEW_ORDERS') && isset($accesses['AdminOrders']) && $accesses['AdminOrders']['view'] ? '1' : false,
        ];

        $controllerConfiguration->templatesVars = array_merge($controllerConfiguration->templatesVars, $notificationsSettings);

        Media::addJsDef($notificationsSettings);
    }
}
