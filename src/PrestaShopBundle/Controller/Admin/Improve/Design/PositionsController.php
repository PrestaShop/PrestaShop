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

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use PrestaShopBundle\Form\Admin\Improve\Design\PositionsFormDataProvider;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Module;
use Hook;
use Shop;

/**
 * Configuration modules positions  "Improve > Design > Positions"
 */
class PositionsController extends FrameworkBundleAdminController
{
    protected $selectedModule = null;

    /**
     * Display hooks positions
     *
     * @Template("@PrestaShop/Admin/Improve/Design/positions.html.twig")
     * @AdminSecurity("is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $context = $this->getContext();
        $configuration = $this->get('prestashop.adapter.legacy.configuration');
        $admin_dir = basename(_PS_ADMIN_DIR_);
        $installedModules = Module::getModulesInstalled();

        $modules = [];
        foreach ($installedModules as $installedModule) {
            if ($module = Module::getInstanceById((int) $installedModule['id_module'])) {
                // We want to be able to sort modules by display name
                $modules[(int) $module->id] = $module;
            }
        }

        $hooks = Hook::getHooks();
        foreach ($hooks as $key => $hook) {
            $hooks[$key]['modules'] = Hook::getModulesFromHook($hook['id_hook'], $this->selectedModule);
            // No module found, no need to continue
            if (!is_array($hooks[$key]['modules'])) {
                unset($hooks[$key]);
                continue;
            }

            foreach ($hooks[$key]['modules'] as $index => $module) {
                if (empty($modules[(int) $module['id_module']])) {
                    unset($hooks[$key]['modules'][$index]);
                }
            }

            $hooks[$key]['modules_count'] = count($hooks[$key]['modules']);
            // No module remaining after the check, no need to continue
            if ($hooks[$key]['modules_count'] === 0) {
                unset($hooks[$key]);
                continue;
            }

            $hooks[$key]['position'] = Hook::isDisplayHookName($hook['name']);
        }

        return [
            'layoutHeaderToolbarBtn' => [
                'save' => [
                    'href' => $this->generateUrl('admin_clear_cache'),
                    'desc' => $this->trans('Transplant a module', 'Admin.Design.Feature'),
                ]
            ],
            'layoutTitle' => $this->trans('Positions', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => false,
            'requireBulkActions' => false,
            'requireFilterStatus' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminModulesPositions'),

            'selectedModule' => $this->selectedModule,
            'hooks' => $hooks,
            'modules' => $modules,
            'canMove' => !(Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP)
        ];
    }

    /**
     * Transplant a module
     *
     * @Template("@PrestaShop/Admin/Improve/Design/positions-form.html.twig")
     * @AdminSecurity("is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request, $moduleId, $hookId)
    {
        /* @var $formHandler FormHandler */
        $formHandler = $this->get('prestashop.adapter.improve.design.positions.form_handler');

        /* @var $dataProvider PositionsFormDataProvider */
        $dataProvider = $this->get('prestashop.adapter.improve.design.positions.form_provider');
        $dataProvider->load(
            $moduleId,
            $hookId
        );

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
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans('Delivery Slips', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => false,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'selectedModule' => []
        ];
    }
}
