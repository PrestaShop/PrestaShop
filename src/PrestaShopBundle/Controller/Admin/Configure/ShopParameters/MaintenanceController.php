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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tools;

/**
 * Responsible of "Configure > Shop Parameters > General > Maintenance" page
 */
class MaintenanceController extends FrameworkBundleAdminController
{
    const CONTROLLER_NAME = 'AdminMaintenance';

    /**
     * @var FormInterface
     * @Template("@PrestaShop/Admin/Configure/ShopParameters/maintenance.html.twig")
     * @return Response
     */
    public function indexAction(Request $request, FormInterface $form = null)
    {
        if (is_null($form)) {
            $form = $this->get('prestashop.adapter.maintenance.form_handler')->getForm();
        }

        return array(
            'layoutHeaderToolbarBtn' => array(),
            'layoutTitle' => $this->trans('Maintenance', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminMaintenance'),
            'requireFilterStatus' => false,
            'form' => $form->createView(),
            'currentIp' => $request->getClientIp(),
        );
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function processFormAction(Request $request)
    {
        $redirectResponse = $this->redirectToRoute('admin_maintenance');
        if ($this->isDemoModeEnabled()) {
            $this->addFlash('error', $this->getDemoModeErrorMessage());

            return $redirectResponse;
        }

        if (!in_array(
            $this->authorizationLevel($this::CONTROLLER_NAME),
            array(
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_UPDATE,
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_DELETE,
            )
        )) {
            $this->addFlash(
                'error',
                $this->trans('You do not have permission to update this.', 'Admin.Notifications.Error')
            );
            return $redirectResponse;
        }

        $this->dispatchHook('actionAdminMaintenanceControllerPostProcessBefore', array('controller' => $this));
        $form = $this->get('prestashop.adapter.maintenance.form_handler')->getForm();
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $redirectResponse;
        }

        $data = $form->getData();
        $saveErrors = $this->get('prestashop.adapter.maintenance.form_handler')->save($data);

        if (0 === count($saveErrors)) {
            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
            return $redirectResponse;
        }

        $this->flashErrors($saveErrors);
        return $redirectResponse;
    }
}
