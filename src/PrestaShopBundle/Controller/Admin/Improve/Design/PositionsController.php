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

        $toolbarButtons = [
            'save' => [
                'href' => $this->generateUrl('admin_clear_cache'),
                'desc' => $this->trans('Transplant a module', 'Admin.Design.Feature'),
            ]
        ];

        return [
            'layoutHeaderToolbarBtn' => $toolbarButtons,
            'layoutTitle' => $this->trans('Positions', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => false,
            'requireBulkActions' => false,
            'requireFilterStatus' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminModulesPositions'),

            'urlShowModules' => '?show_modules=',
            'urlShowInvisible' => '?show_modules='.(int)$request->query->get('show_modules').'&hook_position=',

            'selectedModule' => $this->selectedModule,
            'hooks' => $hooks,
            'modules' => $modules,
            'canMove' => (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) ? false : true,
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

    // public function ajaxProcessSaveHook()
    // {
    //     if ($this->access('edit')) {
    //         /* PrestaShop demo mode */
    //         if (_PS_MODE_DEMO_) {
    //             die('{"hasError" : true, "errors" : ["Live Edit: This functionality has been disabled."]}');
    //         }

    //         $hooks_list = explode(',', Tools::getValue('hooks_list'));
    //         $id_shop = (int)Tools::getValue('id_shop');
    //         if (!$id_shop) {
    //             $id_shop = Context::getContext()->shop->id;
    //         }

    //         $res = true;
    //         $hookableList = [];
    //         // $_POST['hook'] is an array of id_module
    //         $hooks_list = Tools::getValue('hook');

    //         foreach ($hooks_list as $id_hook => $modules) {
    //             // 1st, drop all previous hooked modules
    //             $sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module` WHERE `id_hook` =  '.(int)$id_hook.' AND id_shop = '.(int)$id_shop;
    //             $res &= Db::getInstance()->execute($sql);

    //             $i = 1;
    //             $value = '';
    //             $ids = [];
    //             // then prepare sql query to rehook all chosen modules(id_module, id_shop, id_hook, position)
    //             // position is i (autoincremented)
    //             if (is_array($modules) && count($modules)) {
    //                 foreach ($modules as $id_module) {
    //                     if ($id_module && !in_array($id_module, $ids)) {
    //                         $ids[] = (int)$id_module;
    //                         $value .= '('.(int)$id_module.', '.(int)$id_shop.', '.(int)$id_hook.', '.(int)$i.'),';
    //                     }
    //                     $i++;
    //                 }

    //                 if ($value) {
    //                     $value = rtrim($value, ',');
    //                     $res &= Db::getInstance()->execute('INSERT INTO  `'._DB_PREFIX_.'hook_module` (id_module, id_shop, id_hook, position) VALUES '.$value);
    //                 }
    //             }
    //         }
    //         if ($res) {
    //             $hasError = true;
    //         } else {
    //             $hasError = false;
    //         }
    //         die('{"hasError" : false, "errors" : ""}');
    //     }
    // }

    // /**
    //  * Return a json array containing the possible hooks for a module.
    //  *
    //  * @return null
    //  */
    // public function ajaxProcessGetPossibleHookingListForModule()
    // {
    //     $module_id = (int)Tools::getValue('module_id');
    //     if ($module_id == 0) {
    //         die('{"hasError" : true, "errors" : ["Wrong module ID."]}');
    //     }

    //     $module_instance = Module::getInstanceById($module_id);
    //     die(json_encode($module_instance->getPossibleHooksList()));
    // }

    // public function postProcess()
    // {
    //     // Getting key value for display
    //     if (Tools::getValue('show_modules') && strval(Tools::getValue('show_modules')) != 'all') {
    //         $this->display_key = (int)Tools::getValue('show_modules');
    //     }

    //     $this->addjQueryPlugin(array(
    //         'select2',
    //     ));

    //     $this->addJS(array(
    //         _PS_JS_DIR_.'admin/modules-position.js',
    //         _PS_JS_DIR_.'jquery/plugins/select2/select2_locale_'.$this->context->language->iso_code.'.js',
    //     ));


    //     // Change position in hook
    //     if (array_key_exists('changePosition', $_GET)) {
    //         if ($this->access('edit')) {
    //             $id_module = (int)Tools::getValue('id_module');
    //             $id_hook = (int)Tools::getValue('id_hook');
    //             $module = Module::getInstanceById($id_module);
    //             if (Validate::isLoadedObject($module)) {
    //                 $module->updatePosition($id_hook, (int)Tools::getValue('direction'));
    //                 Tools::redirectAdmin(self::$currentIndex.($this->display_key ? '&show_modules='.$this->display_key : '').'&token='.$this->token);
    //             } else {
    //                 $this->errors[] = $this->trans('This module cannot be loaded.', [], 'Admin.Modules.Notification');
    //             }
    //         } else {
    //             $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
    //         }
    //     } elseif (Tools::isSubmit('submitAddToHook')) {
    //         // Add new module in hook
    //         if ($this->access('add')) {
    //             // Getting vars...
    //             $id_module = (int)Tools::getValue('id_module');
    //             $module = Module::getInstanceById($id_module);
    //             $id_hook = (int)Tools::getValue('id_hook');
    //             $hook = new Hook($id_hook);

    //             if (!$id_module || !Validate::isLoadedObject($module)) {
    //                 $this->errors[] = $this->trans('This module cannot be loaded.', [], 'Admin.Modules.Notification');
    //             } elseif (!$id_hook || !Validate::isLoadedObject($hook)) {
    //                 $this->errors[] = $this->trans('Hook cannot be loaded.', [], 'Admin.Modules.Notification');
    //             } elseif (Hook::getModulesFromHook($id_hook, $id_module)) {
    //                 $this->errors[] = $this->trans('This module has already been transplanted to this hook.', [], 'Admin.Modules.Notification');
    //             } elseif (!$module->isHookableOn($hook->name)) {
    //                 $this->errors[] = $this->trans('This module cannot be transplanted to this hook.', [], 'Admin.Modules.Notification');
    //             } else {
    //                 // Adding vars...
    //                 if (!$module->registerHook($hook->name, Shop::getContextListShopID())) {
    //                     $this->errors[] = $this->trans('An error occurred while transplanting the module to its hook.', [], 'Admin.Modules.Notification');
    //                 } else {
    //                     $exceptions = Tools::getValue('exceptions');
    //                     $exceptions = (isset($exceptions[0])) ? $exceptions[0] : [];
    //                     $exceptions = explode(',', str_replace(' ', '', $exceptions));
    //                     $exceptions = array_unique($exceptions);

    //                     foreach ($exceptions as $key => $except) {
    //                         if (empty($except)) {
    //                             unset($exceptions[$key]);
    //                         } elseif (!empty($except) && !Validate::isFileName($except)) {
    //                             $this->errors[] = $this->trans('No valid value for field exceptions has been defined.', [], 'Admin.Notifications.Error');
    //                         }
    //                     }
    //                     if (!$this->errors && !$module->registerExceptions($id_hook, $exceptions, Shop::getContextListShopID())) {
    //                         $this->errors[] = $this->trans('An error occurred while transplanting the module to its hook.', [], 'Admin.Notifications.Error');
    //                     }
    //                 }
    //                 if (!$this->errors) {
    //                     Tools::redirectAdmin(self::$currentIndex.'&conf=16'.($this->display_key ? '&show_modules='.$this->display_key : '').'&token='.$this->token);
    //                 }
    //             }
    //         } else {
    //             $this->errors[] = $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error');
    //         }
    //     } elseif (Tools::isSubmit('submitEditGraft')) {
    //         // Edit module from hook
    //         if ($this->access('add')) {
    //             // Getting vars...
    //             $id_module = (int)Tools::getValue('id_module');
    //             $module = Module::getInstanceById($id_module);
    //             $id_hook = (int)Tools::getValue('id_hook');
    //             $new_hook = (int)Tools::getValue('new_hook');
    //             $hook = new Hook($new_hook);

    //             if (!$id_module || !Validate::isLoadedObject($module)) {
    //                 $this->errors[] = $this->trans('This module cannot be loaded.', [], 'Admin.Modules.Notification');
    //             } elseif (!$id_hook || !Validate::isLoadedObject($hook)) {
    //                 $this->errors[] = $this->trans('Hook cannot be loaded.', [], 'Admin.Modules.Notification');
    //             } else {
    //                 if ($new_hook !== $id_hook) {
    //                     /** Connect module to a newer hook */
    //                     if (!$module->registerHook($hook->name, Shop::getContextListShopID())) {
    //                         $this->errors[] = $this->trans('An error occurred while transplanting the module to its hook.', [], 'Admin.Modules.Notification');
    //                     }
    //                     /** Unregister module from hook & exceptions linked to module */
    //                     if (!$module->unregisterHook($id_hook, Shop::getContextListShopID())
    //                         || !$module->unregisterExceptions($id_hook, Shop::getContextListShopID())) {
    //                         $this->errors[] = $this->trans('An error occurred while deleting the module from its hook.', [], 'Admin.Modules.Notification');
    //                     }
    //                     $id_hook = $new_hook;
    //                 }
    //                 $exceptions = Tools::getValue('exceptions');
    //                 if (is_array($exceptions)) {
    //                     foreach ($exceptions as $id => $exception) {
    //                         $exception = explode(',', str_replace(' ', '', $exception));
    //                         $exception = array_unique($exception);
    //                         // Check files name
    //                         foreach ($exception as $except) {
    //                             if (!empty($except) && !Validate::isFileName($except)) {
    //                                 $this->errors[] = $this->trans('No valid value for field exceptions has been defined.', [], 'Admin.Notifications.Error');
    //                             }
    //                         }

    //                         $exceptions[$id] = $exception;
    //                     }

    //                     // Add files exceptions
    //                     if (!$module->editExceptions($id_hook, $exceptions)) {
    //                         $this->errors[] = $this->trans('An error occurred while transplanting the module to its hook.', [], 'Admin.Modules.Notification');
    //                     }

    //                     if (!$this->errors) {
    //                         Tools::redirectAdmin(self::$currentIndex.'&conf=16'.($this->display_key ? '&show_modules='.$this->display_key : '').'&token='.$this->token);
    //                     }
    //                 } else {
    //                     $exceptions = explode(',', str_replace(' ', '', $exceptions));
    //                     $exceptions = array_unique($exceptions);

    //                     // Check files name
    //                     foreach ($exceptions as $except) {
    //                         if (!empty($except) && !Validate::isFileName($except)) {
    //                             $this->errors[] = $this->trans('No valid value for field exceptions has been defined.', [], 'Admin.Notifications.Error');
    //                         }
    //                     }

    //                     // Add files exceptions
    //                     if (!$module->editExceptions($id_hook, $exceptions, Shop::getContextListShopID())) {
    //                         $this->errors[] = $this->trans('An error occurred while transplanting the module to its hook.', [], 'Admin.Modules.Notification');
    //                     } else {
    //                         Tools::redirectAdmin(self::$currentIndex.'&conf=16'.($this->display_key ? '&show_modules='.$this->display_key : '').'&token='.$this->token);
    //                     }
    //                 }
    //             }
    //         } else {
    //             $this->errors[] = $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error');
    //         }
    //     } elseif (array_key_exists('deleteGraft', $_GET)) {
    //         // Delete module from hook
    //         if ($this->access('delete')) {
    //             $id_module = (int)Tools::getValue('id_module');
    //             $module = Module::getInstanceById($id_module);
    //             $id_hook = (int)Tools::getValue('id_hook');
    //             $hook = new Hook($id_hook);
    //             if (!Validate::isLoadedObject($module)) {
    //                 $this->errors[] = $this->trans('This module cannot be loaded.', [], 'Admin.Modules.Notification');
    //             } elseif (!$id_hook || !Validate::isLoadedObject($hook)) {
    //                 $this->errors[] = $this->trans('Hook cannot be loaded.', [], 'Admin.Modules.Notification');
    //             } else {
    //                 if (!$module->unregisterHook($id_hook, Shop::getContextListShopID())
    //                     || !$module->unregisterExceptions($id_hook, Shop::getContextListShopID())) {
    //                     $this->errors[] = $this->trans('An error occurred while deleting the module from its hook.', [], 'Admin.Modules.Notification');
    //                 } else {
    //                     Tools::redirectAdmin(self::$currentIndex.'&conf=17'.($this->display_key ? '&show_modules='.$this->display_key : '').'&token='.$this->token);
    //                 }
    //             }
    //         } else {
    //             $this->errors[] = $this->trans('You do not have permission to delete this.', [], 'Admin.Notifications.Error');
    //         }
    //     } elseif (Tools::isSubmit('unhookform')) {
    //         if (!($unhooks = Tools::getValue('unhooks')) || !is_array($unhooks)) {
    //             $this->errors[] = $this->trans('Please select a module to unhook.', [], 'Admin.Modules.Notification');
    //         } else {
    //             foreach ($unhooks as $unhook) {
    //                 $explode = explode('_', $unhook);
    //                 $id_hook = $explode[0];
    //                 $id_module = $explode[1];
    //                 $module = Module::getInstanceById((int)$id_module);
    //                 $hook = new Hook((int)$id_hook);
    //                 if (!Validate::isLoadedObject($module)) {
    //                     $this->errors[] = $this->trans('This module cannot be loaded.', [], 'Admin.Modules.Notification');
    //                 } elseif (!$id_hook || !Validate::isLoadedObject($hook)) {
    //                     $this->errors[] = $this->trans('Hook cannot be loaded.', [], 'Admin.Modules.Notification');
    //                 } else {
    //                     if (!$module->unregisterHook((int)$id_hook) || !$module->unregisterExceptions((int)$id_hook)) {
    //                         $this->errors[] = $this->trans('An error occurred while deleting the module from its hook.', [], 'Admin.Modules.Notification');
    //                     }
    //                 }
    //             }
    //             if (!count($this->errors)) {
    //                 Tools::redirectAdmin(self::$currentIndex.'&conf=17'.($this->display_key ? '&show_modules='.$this->display_key : '').'&token='.$this->token);
    //             }
    //         }
    //     } else {
    //         parent::postProcess();
    //     }
    // }
}
