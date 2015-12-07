<?php

namespace PrestaShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function moduleAction(Request $request)
    {
        $action = $request->attributes->get('action');
        $modules = array($request->attributes->get('module_name'));
        $action = implode(array($action, 'Module'));

        $ret = array();
        if (method_exists($this, $action)) {
            $ret = array_combine($modules, array_map(array($this, $action), $modules));
        } else {
            return new Response('Invalid action', 200, array( 'Content-Type' => 'application/json' ));
        }

        if ($request->isXmlHttpRequest()) {
            $res = json_encode($ret);
            return new Response(empty($res) ? '[]' : $res, 200, array( 'Content-Type' => 'application/json' ));
        }

        return $this->redirect($this->generateUrl('admin_module_catalog'));
    }

    /**
     * Controller resposible for displaying "Catalog" section of Module management pages
     * @param  Request $request
     * @return Response
     */
    public function importModuleAction(Request $request)
    {
        $action = $request->attributes->get('action');
        $moduleName = $request->attributes->get('module_name').'.zip';

        $response = new StreamedResponse();

        // $file = $request->files->get($moduleName);

        $file = 'test';
        // $file = false;

        if ($file) {
            // $file->move(_PS_CACHE_DIR_.'tmp'.DIRECTORY_SEPARATOR.'upload', $fileName);
            $fileName = md5(uniqid()) . '.zip';

            ob_flush();
            $response->setCallback(function () use ($moduleName, $fileName) {

                if (!$this->unzipTest($fileName)) {
                    return json_encode(array('status' => false, 'msg' => 'Cannot unzip module'));
                }

                echo json_encode(array('status' => true, 'msg' => 'module extracted'));
                ob_flush();
                flush();

                if (!$this->unzipTest($moduleName)) {
                    return json_encode(array('status' => false, 'msg' => 'invalid module'));
                }

                echo json_encode(array('status' => true, 'msg' => 'module ready to intall'));
                ob_flush();
                flush();

                if (!$this->unzipTest($fileName)) {
                    return json_encode(array('status' => false, 'msg' => 'Cannot install module'));
                }
               
                echo json_encode(array('status' => true, 'msg' => 'module isntalled'));
                ob_flush();
                flush();

            });

            return $response->send();
        }

        return new JsonResponse(array('status' => false, 'msg' => 'invalid file'), 200, array( 'Content-Type' => 'application/json' ));
    }

    final private function unzipTest($fileName)
    {
        sleep(2);
        // unsip process go here
        return true;
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
        $status = 'ok!';
        $msg = sprintf('Module %s is now installed', $module_name);

        // sleep(2);

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
