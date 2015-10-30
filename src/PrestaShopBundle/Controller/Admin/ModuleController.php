<?php

namespace PrestaShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ModuleController extends Controller
{
    /**
     * Controller resposible for displaying "Catalog" section of Module management pages
     * @param  Request $request
     * @return Response
     */
    public function catalogAction(Request $request)
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
        //die(var_dump($modulesProvider->getAllModules()));
        return $this->render('PrestaShopBundle:Admin/Module:catalog.html.twig', array(
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'modules' => $this->createCatalogModuleList($modulesProvider->getCatalogModules()),
                'topMenuData' => $this->getTopMenuData()
            ));
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
}
