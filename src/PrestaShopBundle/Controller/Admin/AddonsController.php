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

namespace PrestaShopBundle\Controller\Admin;

use Configuration;
use Exception;
use PhpEncryption;
use PrestaShop\PrestaShop\Core\Addon\Login\Exception\LoginErrorException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AddonsController extends FrameworkBundleAdminController
{
    /**
     * Controller responsible of the authentication on PrestaShop Addons.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function loginAction(Request $request)
    {
        $addonsProvider = $this->get('prestashop.core.admin.data_provider.addons_interface');
        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $response = new JsonResponse();

        // Parameters needed in order to authenticate the merchant : login and password
        $params = [
            'format' => 'json',
            'username_addons' => $request->request->get('username_addons', null),
            'password_addons' => $request->request->get('password_addons', null),
        ];

        try {
            $json = $addonsProvider->request('check_customer', $params);
            if ($json === null) {
                throw new LoginErrorException();
            }

            if (!empty($json->errors)) {
                throw new LoginErrorException($json->errors->code . ': ' . $json->errors->label);
            }

            Configuration::updateValue('PS_LOGGED_ON_ADDONS', 1);

            $phpEncryption = new PhpEncryption(_NEW_COOKIE_KEY_);

            $response->headers->setCookie(
                new Cookie('username_addons', $phpEncryption->encrypt($params['username_addons']))
            );
            $response->headers->setCookie(
                new Cookie('password_addons', $phpEncryption->encrypt($params['password_addons']))
            );
            $response->headers->setCookie(
                new Cookie('is_contributor', (int) $json->is_contributor)
            );

            $response->setData(['success' => 1, 'message' => '']);
            $modulesProvider->clearCatalogCache();
        } catch (Exception $e) {
            $response->setData([
                'success' => 0,
                'message' => $this->trans(
                    'PrestaShop was unable to log in to Addons. Please check your credentials and your Internet connection.',
                    'Admin.Notifications.Error'
                ),
            ]);
        }

        return $response;
    }

    /**
     * Controller responsible of the authentication on PrestaShop Addons.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logoutAction(Request $request)
    {
        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $modulesProvider->clearCatalogCache();

        if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse();
            $response->setData([
                'success' => 1,
                'message' => '',
            ]);
        } else {
            if ($request->server->get('HTTP_REFERER')) {
                $url = $request->server->get('HTTP_REFERER');
            } else {
                $url = $this->redirect($this->generateUrl('admin_module_catalog'));
            }
            $response = new RedirectResponse($url);
        }
        $response->headers->clearCookie('username_addons');
        $response->headers->clearCookie('password_addons');
        $response->headers->clearCookie('is_contributor');

        return $response;
    }
}
