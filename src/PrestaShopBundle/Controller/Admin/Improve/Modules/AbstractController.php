<?php
/*
 * 2007-2018 PrestaShop
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * 
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2018 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Improve\Modules;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Voter\PageVoter;

abstract class AbstractController extends FrameworkBundleAdminController
{
    const CONTROLLER_NAME = 'ADMINMODULESSF';

    protected function getToolbarButtons()
    {
        // toolbarButtons
        $toolbarButtons = array();

        if (!in_array(
            $this->authorizationLevel($this::CONTROLLER_NAME),
            array(
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_UPDATE,
            )
        )) {
            $toolbarButtons['add_module'] = array(
                'href' => '#',
                'desc' => $this->trans('Upload a module', 'Admin.Modules.Feature'),
                'icon' => 'cloud_upload',
                'help' => $this->trans('Upload a module', 'Admin.Modules.Feature'),
            );
        }

        return array_merge($toolbarButtons, $this->getAddonsConnectToolbar());
    }

    private function getAddonsConnectToolbar()
    {
        $addonsProvider = $this->get('prestashop.core.admin.data_provider.addons_interface');
        $addonsConnect = array();

        if ($addonsProvider->isAddonsAuthenticated()) {
            $addonsEmail = $addonsProvider->getAddonsEmail();
            $addonsConnect['addons_logout'] = array(
                'href' => '#',
                'desc' => $addonsEmail['username_addons'],
                'icon' => 'exit_to_app',
                'help' => $this->trans('Synchronized with Addons marketplace!', 'Admin.Modules.Notification'),
            );
        } else {
            $addonsConnect['addons_connect'] = array(
                'href' => '#',
                'desc' => $this->trans('Connect to Addons marketplace', 'Admin.Modules.Feature'),
                'icon' => 'vpn_key',
                'help' => $this->trans('Connect to Addons marketplace', 'Admin.Modules.Feature'),
            );
        }

        return $addonsConnect;
    }
}
