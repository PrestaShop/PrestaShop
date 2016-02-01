<?php

namespace PrestaShopBundle\Controller\Admin;

use Configuration;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AddonsController extends Controller
{
    /**
     * Controller responsible of the authentication on PrestaShop Addons
     * @param  Request $request
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $addonsProvider = $this->container->get('prestashop.core.admin.data_provider.addons_interface');
        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');
        $response = new JsonResponse();

        // Parameters needed in order to authenticate the merchant : login and password
        $params = [
            'format' => 'json',
            'username_addons' => $request->request->get('username_addons', null),
            'password_addons' => $request->request->get('password_addons', null),
        ];

        try {
            $json = $addonsProvider->request('check_customer', $params);

            Configuration::updateValue('PS_LOGGED_ON_ADDONS', 1);

            $response->headers->setCookie(new Cookie('username_addons', $params['username_addons']));
            $response->headers->setCookie(new Cookie('password_addons', $params['password_addons']));
            $response->headers->setCookie(new Cookie('is_contributor', (int)$json->is_contributor));

            $response->setData(['success' => 1, 'message' => '']);
            $modulesProvider->clearCatalogCache();
        } catch (Exception $e) {
            $response->setData([
                'success' => 0,
                'message' => $e->getMessage(),
            ]);
        }

        return $response;
    }

    /**
     * Controller responsible of the authentication on PrestaShop Addons
     * @param  Request $request
     * @return Response
     */
    public function logoutAction()
    {
        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');
        $modulesProvider->clearCatalogCache();

        $response = new JsonResponse();
        $response->headers->clearCookie('username_addons');
        $response->headers->clearCookie('password_addons');
        $response->headers->clearCookie('is_contributor');
        $response->setData([
                'success' => 1,
                'message' => ''
            ]);
        return $response;
    }
}
