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
                'modules' => $this->createCatalogModuleList($modulesProvider->getAllModules())
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

    final private function createCatalogModuleList(array $module_full_list)
    {
        foreach ($module_full_list as $key => $module) {
            if ((bool)$module->installed === true) {
                unset($module_full_list[$key]);
            }

            // @TODO: Check why some of the module dont have any image attached, meanwhile just remove it from the list
            if (!isset($module->image)) {
                unset($module_full_list[$key]);
            }
        }

        return $module_full_list;
    }
}
