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

use OrderInvoice;
use Validate;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Exception\FileUploadException;
use PrestaShopBundle\Form\Admin\Order\Delivery\SlipOptionsType;
use PrestaShopBundle\Security\Voter\PageVoter;
use PrestaShop\PrestaShop\Core\Form\FormHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Admin controller for the Order Delivery
 */
class DeliveryController extends FrameworkBundleAdminController
{
    /**
     *
     * @Template("@PrestaShop/Admin/Order/Delivery/slip.html.twig")
     * @param Request $request
     *
     * @return array|RedirectResponse
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

        /* @var $formHandler FormHandler */
        $formHandler = $this->get('prestashop.adapter.order.delivery.slip.form_handler');
        /* @var $form Form */
        $form = $formHandler->getForm();

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
            'layoutTitle' => $this->trans('Delivery Slips', 'Admin.NavigationMenu'),
            'requireAddonsSearch' => false,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
        ];
    }

    /**
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function generatePdfAction(Request $request)
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

        $formData = $request->request->get('form');
        $dateFrom = !empty($formData['pdf']['date_from']) ?
                  $formData['pdf']['date_from'] :
                  null;
        $dateTo = !empty($formData['pdf']['date_to']) ?
                  $formData['pdf']['date_to'] :
                  null;

        $errors = [];
        if (!Validate::isDate($dateFrom)) {
            $errors[] = [
                'key' => "Invalid 'from' date",
                'domain' => 'Admin.Catalog.Notification',
                'parameters' => [],
            ];
        }
        if (!Validate::isDate($dateTo)) {
            $errors[] = [
                'key' => "Invalid 'to' date",
                'domain' => 'Admin.Catalog.Notification',
                'parameters' => [],
            ];
        }

        if (empty($errors)) {
            if (!empty(OrderInvoice::getByDeliveryDateInterval($dateFrom, $dateTo))) {
                return $this->redirect(
                    $this->get('prestashop.adapter.legacy.context')
                    ->getAdminLink('AdminPdf') .
                    '&submitAction=generateDeliverySlipsPDF&date_from=' .
                    urlencode($dateFrom) .'&date_to=' . urlencode($dateTo)
                );
            }

            $errors[] = [
                'key' => 'No delivery slip was found for this period.',
                'domain' => 'Admin.Orderscustomers.Notification',
                'parameters' => [],
            ];
        }

        if (!empty($errors)) {
            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('admin_order_delivery_slip');
    }
}
