<?php

namespace PrestaShopBundle\Controller\Admin;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
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
            'href' => '#',
            'desc' => $translator->trans('Add a module', array(), $request->attributes->get('_legacy_controller')),
            'icon' => 'process-icon-new',
            'help' => $translator->trans('Add a module', array(), $request->attributes->get('_legacy_controller')),
            'class' => 'slut',
            'id' => 'test',
            'test' => 'test'
        );

        //<a class="module-read-more-grid-btn" href="#" data-toggle="modal" data-target="#module-modal-read-more">Read More</a>
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
        $module = $request->attributes->get('module_name');

        $ret = array();
        if (method_exists($this, $action)) {
            try {
                // ToDo : Check if allowed to call this action
                $ret[$module] = $this->{$action}($module);
            } catch (Exception $e) {
                $ret[$module]['status'] = false;
                $ret[$module]['msg'] = $e->getMessage();
            }
        } else {
            $ret[$module]['status'] = false;
            $ret[$module]['msg'] = 'Invalid action';
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($ret, 200);
        }

        // We need a better error handler here. Meanwhile, I throw an exception
        if (! $ret[$module]['status']) {
            throw new Exception($ret[$module]['msg']);
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
        $file = $request->files->get('module_file');
        $orinal_file_name = $file->getClientOriginalName();
        $module_name = basename($orinal_file_name, '.'.$file->guessExtension());

        if ($file) {
            $file_name = md5(uniqid()).'.zip';
            $file->move(_PS_CACHE_DIR_.'tmp'.DIRECTORY_SEPARATOR.'upload', $file_name);

            if (!$this->unzipModule($file_name, $module_name)) {
                // @TODO: clean file
                return new JsonResponse(array('status' => false, 'msg' => 'unzip failed'), 200, array( 'Content-Type' => 'application/json' ));
            }

            if (!$this->installModule($module_name)) {
                // @TODO: clean file
                return new JsonResponse(array('status' => false, 'msg' => 'Module Failed to install'), 200, array( 'Content-Type' => 'application/json' ));
            }

            return new JsonResponse(array('status' => true, 'msg' => $module_name.' successfully installed', 'module_name' => $module_name), 200, array( 'Content-Type' => 'application/json' ));
            ;
        }

        return new JsonResponse(array('status' => false, 'msg' => 'invalid file'), 200, array( 'Content-Type' => 'application/json' ));
    }

    final private function unzipModule($file_name, $module_name)
    {
        $file = _PS_CACHE_DIR_.'tmp'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.$file_name;

        if (file_exists($file)) {
            $zip_archive = new \ZipArchive();
            $ret = $zip_archive->open($file);

            if ($ret === true) {
                $zip_archive ->extractTo(_PS_MODULE_DIR_);
                $zip_archive ->close();
                return true;
            }
        }

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
                throw new Exception("Menu '$activeMenu' not found in Top Menu data", 1);
            } else {
                $topMenuData->{$activeMenu}->class = 'active';
            }
        }

        return (array)$topMenuData;
    }

    protected function installModule($module_name)
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

    protected function uninstallModule($module_name)
    {
        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');

        // Module uninstall
        $module = $modulesProvider->getModule($module_name);
        $status = $module->uninstall();

        if ($status) {
            // Module files deletion
            $fs = new Filesystem();
            $fs->remove(_PS_MODULE_DIR_.$module_name);

            $msg = sprintf('Module %s is now uninstalled', $module_name);
        } else {
            $msg = sprintf('Could not uninstall module %s (Additionnal Information: %s)', $module_name, join(', ', $module->getErrors()));
        }

        return array('status' => $status, 'msg' => $msg);
    }

    public function configureModuleAction($module_name)
    {
        $modulesProvider    = $this->container->get('prestashop.core.admin.data_provider.module_interface');
        $legacyUrlGenerator = $this->container->get('prestashop.core.admin.url_generator_legacy');

        /* @var $legacyUrlGenerator UrlGeneratorInterface */
        $redirectionParams = array(
            // do not transmit limit & offset: go to the first page when redirecting
            'configure' => $module_name,
        );
        return $this->redirect($legacyUrlGenerator->generate('admin_module_configure_action',
                    $redirectionParams), 302);
    }

    protected function enableModule($module_name)
    {
        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');
        $module = $modulesProvider->getModule($module_name);
        $status = $module->enable();

        if ($status) {
            $msg = sprintf('Module %s is now enabled', $module_name);
        } else {
            $msg = sprintf('Could not enable module %s (Additionnal Information: %s)', $module_name, join(', ', $module->getErrors()));
        }

        return array('status' => $status, 'msg' => $msg);
    }

    protected function disableModule($module_name)
    {
        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');
        $module = $modulesProvider->getModule($module_name);
        $status = $module->disable();

        if ($status) {
            $msg = sprintf('Module %s is now disabled', $module_name);
        } else {
            $msg = sprintf('Could not disable module %s (Additionnal Information: %s)', $module_name, join(', ', $module->getErrors()));
        }

        return array('status' => $status, 'msg' => $msg);
    }

    protected function resetModule($module_name)
    {
        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');
        $request = Request::createFromGlobals();

        $module = $modulesProvider->getModule($module_name);
        if ($request->request->has('keep_data') && method_exists($this, 'reset')) {
            $status = $module->disable();
        } else {
            $status = ($module->uninstall() && $module->install());
        }

        if ($status) {
            $msg = sprintf('Module %s has been reset', $module_name);
        } else {
            $msg = sprintf('Could not reset module %s (Additionnal Information: %s)', $module_name, join(', ', $module->getErrors()));
        }

        return array('status' => $status, 'msg' => $msg);
    }

    protected function updateModule($module_name)
    {
        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');
        $module = $modulesProvider->getModule($module_name);
        $old_version = $module->version;

        $modulesProvider->setModuleOnDiskFromAddons($module_name);
        $module = $modulesProvider->getModule($module_name);
        $new_version = $module->version;

        $status = version_compare($old_version, $new_version, '>');
        if ($status) {
            $msg = sprintf('Module %s has been updated from %s to %s', $module_name, $old_version, $new_version);
        } else {
            $msg = sprintf('Could not update module %s (Version unchanged)', $module_name);
        }

        return array('status' => $status, 'msg' => $msg);
    }
}
