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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible of "Configure > Advanced Parameters > Webservice" page display
 */
class WebserviceController extends FrameworkBundleAdminController
{
    const CONTROLLER_NAME = 'AdminWebservice';

    /**
     * Displays the Webservice main page.
     * @Template("@PrestaShop/Admin/Configure/AdvancedParameters/webservice.html.twig")
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @param FormInterface $form
     * @return Response
     */
    public function indexAction(FormInterface $form = null)
    {
        $form = is_null($form) ? $this->getFormHandler()->getForm() : $form;

        return [
            'layoutTitle' => $this->trans('Webservice', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => false,
            'requireBulkActions' => false, // temporary
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminWebservice'),
            'requireFilterStatus' => false,
            'form' => $form->createView(),
        ];
    }

    /**
     * Process the Webservice configuration form.
     * @AdminSecurity("is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller')~'_')", message="You do not have permission to update this.", redirectRoute="admin_webservice")
     * @DemoRestricted(redirectRoute="admin_webservice")
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function processFormAction(Request $request)
    {
        $this->dispatchHook('actionAdminAdminWebserviceControllerPostProcessBefore', array('controller' => $this));

        $form = $this->getFormHandler()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $saveErrors = $this->getFormHandler()->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_webservice');
            }

            $this->flashErrors($saveErrors);
        }

        return $this->redirectToRoute('admin_webservice');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getFormHandler()
    {
        return $this->get('prestashop.adapter.webservice.form_handler');
    }
}
