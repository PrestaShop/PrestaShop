<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Controller\Admin\Order;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Exception\FileUploadException;
use PrestaShopBundle\Security\Voter\PageVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PrestaShopBundle\Form\Admin\Order\Delivery\SlipOptionsType;

/**
 * Admin controller for the Order Delivery
 */
class DeliveryController extends FrameworkBundleAdminController
{
    /**
     *
     *
     * @Template("@PrestaShop/Admin/Order/Delivery/slip.html.twig")
     * @param Request $request
     *
     * @return string
     */
    public function slipAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');
        if (!in_array(
            $this->authorizationLevel($legacyController),
            [
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_UPDATE,
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_DELETE,
            ]
        )) {
            return $this->redirectToDefaultPage();
        }

        $formHandler = $this->get('prestashop.adapter.order.delivery.slip.form_handler');
        $form = $formHandler->getForm();

        /* @var $form Form */
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $formHandler->save($form->getData());
            if (empty($errors)) {
                $this->addFlash(
                    'success',
                    $this->trans('Update successful', 'Admin.Notifications.Success')
                );
            } else {
                $this->flashErrors($errors);
            }

            return $this->redirectToRoute('admin_order_delivery_slip');
        }

        return [
            'form' => $form->createView(),
            'help_link' => $this->generateSidebarLink($legacyController),
            'layoutTitle' => $this->trans('Delivery Slips', 'Admin.Global'),
            'requireAddonsSearch' => false,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
        ];
    }
}
