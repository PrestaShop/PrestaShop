<?php

namespace PrestaShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ModuleController extends Controller
{
    /**
     * Controller resposible for displaying "Catalog" section of Module management pages
     * @param  Request $request
     * @return Response
     */
    public function catalogAction(Request $request, $category = null, $keyword = null)
    {
        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');
        $translator = $this->container->get('prestashop.adapter.translator');
        // toolbarButtons
        $toolbarButtons = array();
        $toolbarButtons['add_module'] = array(
            'href' => $this->generateUrl('admin_module_import'),
            'desc' => $translator->trans('Add a module', array(), $request->attributes->get('_legacy_controller')),
            'icon' => 'process-icon-new',
            'help' => $translator->trans('Add a module', array(), $request->attributes->get('_legacy_controller'))
        );

        $filter = [];
        if ($keyword !== null) {
            $filter['search'] = $keyword;
        }
        if ($category !== null) {
            $filter['category'] = $category;
        }

        return $this->render('PrestaShopBundle:Admin/Module:catalog.html.twig', array(
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'modules' => $this->createCatalogModuleList($modulesProvider->getCatalogModules($filter)),
                'topMenuData' => $this->getTopMenuData()
            ));
    }

    public function moduleAction(Request $request)
    {
        $action = $request->attributes->get('action'). 'Module';
        $module = $request->attributes->get('module_name');

        $ret = array();
        if (method_exists($this, $action)) {
            // ToDo : Check if allowed to call this action
            $ret[$module] = $this->{$action}($module);
        } else {
            return new JsonResponse('Invalid action', 200);
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($ret, 200);
        }

        // We need a better error handler here. Meanwhile, I throw an exception
        if (! $ret[$module]['status']) {
            throw new \Exception($ret[$module]['msg']);
        }
        return $this->redirect($this->generateUrl('admin_module_catalog'));
    }

     /**
      * Controller resposible for displaying "Catalog" section of Module management pages
      * @param  Request $request
      * @return Response
      */
    public function importAction(Request $request)
    {
        $translator = $this->container->get('prestashop.adapter.translator');
        // toolbarButtons
        $toolbarButtons = array();
        $toolbarButtons['add_module'] = array(
            'href' => $this->generateUrl('admin_module_import'),
            'desc' => $translator->trans('Add a module', array(), $request->attributes->get('_legacy_controller')),
            'icon' => 'process-icon-new',
            'help' => $translator->trans('Add a module', array(), $request->attributes->get('_legacy_controller'))
        );
        return $this->render('PrestaShopBundle:Admin/Module:import.html.twig', array(
                'layoutHeaderToolbarBtn' => $toolbarButtons
            ));
    }

    final private function createCatalogModuleList(array $moduleFullList)
    {
        $installed_modules = [];
        array_map(function ($module) use (&$installed_modules) {
            $installed_modules[$module['name']] = $module;
        }, \Module::getModulesInstalled());

        foreach ($moduleFullList as $key => $module) {
            if ((bool)array_key_exists($module->name, $installed_modules) === true) {
                unset($moduleFullList[$key]);
            }

            // @TODO: Check why some of the module dont have any image attached, meanwhile just remove it from the list
            if (!isset($module->media->img)) {
                unset($moduleFullList[$key]);
            }
        }

        return $moduleFullList;
    }

    final private function getTopMenuData($activeMenu = null)
    {
        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');
        //@TODO: To be made ultra flexible, hardcoded for dev purpose ATM
        $topMenuData = $modulesProvider->getCatalogCategories();

        if (isset($activeMenu)) {
            if (!isset($topMenuData->{$activeMenu})) {
                throw new \Exception("Menu '$activeMenu' not found in Top Menu data", 1);
            } else {
                $topMenuData->{$activeMenu}->class = 'active';
            }
        }

        return (array)$topMenuData;
    }

    public function installModule($module_name)
    {
        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');
        if (! $modulesProvider->isModuleOnDisk($module_name)) {
            $modulesProvider->setModuleOnDiskFromAddons($module_name);
        }

        $module = $modulesProvider->getModule($module_name);
        $status = $module->install();
        if ($status) {
            $msg = sprintf('Module %s is now installed', $module_name);
        } else {
            $msg = sprintf('Could not install module %s (Additionnal Information: %s)', $module_name, join(', ', $module->getErrors()));
        }

        return array('status' => $status, 'msg' => $msg);
    }

    public function uninstallModule($module_name)
    {
        $status = 'ok';
        $msg = sprintf('Module %s is now installed', $module_name);

        // sleep(2);

        return array('status' => $status, 'msg' => $msg);
    }

    public function configureModule($module_name)
    {
        $msg = sprintf('Module %s is now configured', $module_name);
        $status = 'ok';

        // sleep(2);

        return array('status' => $status, 'msg' => $msg);
    }

    public function enableModule($module_name)
    {
        $msg = sprintf('Module %s is now enabled', $module_name);
        $status = 'ok';

        // sleep(2);

        return array('status' => $status, 'msg' => $msg);
    }

    public function disableModule($module_name)
    {
        $msg = sprintf('Module %s is now disabled', $module_name);
        $status = 'ok';

        // sleep(2);

        return array('status' => $status, 'msg' => $msg);
    }

    public function resetModule($module_name)
    {
        $msg = sprintf('Module %s is now reseted', $module_name);
        $status = 'ok';

        // sleep(2);

        return array('status' => $status, 'msg' => $msg);
    }

    public function updateModule($module_name)
    {
        $msg = sprintf('Module %s is now updated', $module_name);
        $status = 'ok';

        // sleep(2);

        return array('status' => $status, 'msg' => $msg);
    }
}
