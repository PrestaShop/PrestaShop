<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin;

use PrestaShopBundle\Service\Routing\Router as PrestaShopRouter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Admin controller to manage security pages.
 */
class SecurityController extends FrameworkBundleAdminController
{
    public function compromisedAccessAction(Request $request)
    {
        $requestUri = urldecode($request->query->get('uri'));

        // getToken() actually generate a new token
        $username = $this->get('prestashop.user_provider')->getUsername();

        $newToken = $this->get('security.csrf.token_manager')
            ->getToken($username)
            ->getValue();

        $newUri = PrestaShopRouter::generateTokenizedUrl($requestUri, $newToken);

        return $this->render('@PrestaShop/Admin/Security/compromised.html.twig', array(
            'requestUri' => $newUri,
        ));
    }
}
