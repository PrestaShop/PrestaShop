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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\SearchShops;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin controller to manage shops.
 */
class ShopController extends FrameworkBundleAdminController
{
    /**
     * Search for shops by query.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchAction(Request $request)
    {
        $searchTerm = $request->query->get('shop_name_search');

        try {
            $shops = $this->getQueryBus()->handle(new SearchShops($searchTerm));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessage($e)],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json($shops);
    }

    /**
     * @param Exception $e
     *
     * @return string
     */
    private function getErrorMessage(Exception $e)
    {
        return $this->getFallbackErrorMessage(
            get_class($e),
            $e->getCode(),
            $e->getMessage()
        );
    }
}
