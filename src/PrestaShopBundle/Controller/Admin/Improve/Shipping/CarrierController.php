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

namespace PrestaShopBundle\Controller\Admin\Improve\Shipping;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier\CarrierType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CarrierController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     */
    public function indexAction()
    {
        //@todo: implemet index action
    }

    /**
     * Show shipping carrier creation page
     *
     * @AdminSecurity("is_granted(['create'], request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $carrierForm = $this->createForm(CarrierType::class);
        $carrierForm->handleRequest($request);

        if ($carrierForm->isSubmitted() && $carrierForm->isValid()) {
            dump($carrierForm->getData());
            die;
        }

        return $this->render('@PrestaShop/Admin/Improve/Shipping/Carrier/add.html.twig', [
            'carrierForm' => $carrierForm->createView(),
            'contextLangId' => $this->container->get('prestashop.adapter.legacy.context')->getContext()->language->id,
            'defaultLangId' => $this->get('prestashop.adapter.legacy.configuration')->getInt('PS_LANG_DEFAULT'),
        ]);
    }
}
