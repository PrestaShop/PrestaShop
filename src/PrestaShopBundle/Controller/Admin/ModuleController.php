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
        $toolbarButtons['manage_module'] = array(
            'href' => $this->generateUrl('admin_module_manage'),
            'desc' => $translator->trans('[TEMP] Manage my modules', array(), get_class($this)),
            'icon' => 'icon-share-square',
            'help' => $translator->trans('Manage', array(), get_class($this)),
        );
        $toolbarButtons['notifications_module'] = array(
            'href' => $this->generateUrl('admin_module_notification'),
            'desc' => $translator->trans('[TEMP] Module notifications', array(), get_class($this)),
            'icon' => 'icon-share-square',
            'help' => $translator->trans('Notifications', array(), get_class($this)),
        );
        $toolbarButtons['add_module'] = array(
            'href' => '#',
            'desc' => $translator->trans('Add a module', array(), get_class($this)),
            'icon' => 'process-icon-new',
            'help' => $translator->trans('Add a module', array(), get_class($this)),
        );

        $filter = [];
        if ($keyword !== null) {
            $filter['search'] = $keyword;
        }
        if ($category !== null) {
            $filter['category'] = $category;
        }

        try {
            $products = $modulesProvider->getCatalogModules($filter);
            shuffle($products);
            $topMenuData = $this->getTopMenuData();
        } catch (Exception $e) {
            $this->addFlash('error', 'Cannot get catalog data. Please try again later.');
            $products = [];
            $topMenuData = [];
        }

        return $this->render('PrestaShopBundle:Admin/Module:catalog.html.twig', array(
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'modules' => $this->generateProductUrls($this->createCatalogModuleList($products)),
                'topMenuData' => $topMenuData
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
            'desc' => $translator->trans('Add a module', array(), get_class($this)),
            'icon' => 'process-icon-new',
            'help' => $translator->trans('Add a module', array(), get_class($this))
        );
        return $this->render('PrestaShopBundle:Admin/Module:import.html.twig', array(
            'layoutHeaderToolbarBtn' => $toolbarButtons
        ));
    }

    public function manageAction(Request $request, $category = null, $keyword = null)
    {
        $translator = $this->container->get('prestashop.adapter.translator');
        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');
        // toolbarButtons
        $toolbarButtons = array();
        $toolbarButtons['catalog_module'] = array(
            'href' => $this->generateUrl('admin_module_catalog'),
            'desc' => $translator->trans('[TEMP] Modules catalog', array(), get_class($this)),
            'icon' => 'icon-share-square',
            'help' => $translator->trans('Catalog', array(), get_class($this)),
        );
        $toolbarButtons['notifications_module'] = array(
            'href' => $this->generateUrl('admin_module_notification'),
            'desc' => $translator->trans('[TEMP] Module notifications', array(), get_class($this)),
            'icon' => 'icon-share-square',
            'help' => $translator->trans('Notifications', array(), get_class($this)),
        );
        $toolbarButtons['add_module'] = array(
            'href' => $this->generateUrl('admin_module_import'),
            'desc' => $translator->trans('Add a module', array(), get_class($this)),
            'icon' => 'process-icon-new',
            'help' => $translator->trans('Add a module', array(), get_class($this))
        );

        $filter = [];
        if ($keyword !== null) {
            $filter['search'] = $keyword;
        }
        if ($category !== null) {
            $filter['category'] = $category;
        }

        $products = new \stdClass;
        foreach (['native_modules', 'theme_bundle', 'modules'] as $subpart) {
            $products->{$subpart} = [];
        }

        foreach ($modulesProvider->getManageModules($filter) as $installed_product) {
            if (isset($installed_product->origin) && $installed_product->origin === 'native' && $installed_product->author === 'PrestaShop') {
                $row = 'native_modules';
            } elseif (0 /* ToDo: insert condition for theme related modules*/) {
                $row = 'theme_bundle';
            } else {
                $row= 'modules';
            }
            $products->{$row}[] = (object)$installed_product;
        }

        foreach ($products as $product_label => $products_part) {
            $products->$product_label = $this->generateProductUrls($products_part);
        }

        return $this->render('PrestaShopBundle:Admin/Module:manage.html.twig', array(
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'modules' => $products,
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
            try {
                $ret[$module] = $this->{$action}($module);
            } catch (Exception $e) {
                $ret[$module]['status'] = false;
                $ret[$module]['msg'] = sprintf('Exception thrown by addon %s on %s. %s', $module, $request->attributes->get('action'), $e->getMessage());

                $logger = $this->get('logger');
                $logger->error($ret[$module]['msg']);
            }
        } else {
            $ret[$module]['status'] = false;
            $ret[$module]['msg'] = 'Invalid action';
        }

        if ($ret[$module]['status']) {
            $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');
            $modulesProvider->clearManageCache();
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($ret, 200);
        }

        // We need a better error handler here. Meanwhile, I throw an exception
        if (! $ret[$module]['status']) {
            $this->addFlash('error', $ret[$module]['msg']);
        } else {
            $this->addFlash('success', $ret[$module]['msg']);
        }

        if ($request->server->get('HTTP_REFERER')) {
            return $this->redirect($request->server->get('HTTP_REFERER'));
        } else {
            return $this->redirect($this->generateUrl('admin_module_catalog'));
        }
    }

    public function notificationAction(Request $request)
    {
        $translator = $this->container->get('prestashop.adapter.translator');
        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');

        // toolbarButtons
        $toolbarButtons = array();
        $toolbarButtons['catalog_module'] = array(
            'href' => $this->generateUrl('admin_module_catalog'),
            'desc' => $translator->trans('[TEMP] Modules catalog', array(), get_class($this)),
            'icon' => 'icon-share-square',
            'help' => $translator->trans('Catalog', array(), get_class($this)),
        );
        $toolbarButtons['manage_module'] = array(
            'href' => $this->generateUrl('admin_module_manage'),
            'desc' => $translator->trans('[TEMP] Manage my modules', array(), get_class($this)),
            'icon' => 'icon-share-square',
            'help' => $translator->trans('Manage', array(), get_class($this)),
        );
        $toolbarButtons['add_module'] = array(
            'href' => $this->generateUrl('admin_module_import'),
            'desc' => $translator->trans('Add a module', array(), get_class($this)),
            'icon' => 'process-icon-new',
            'help' => $translator->trans('Add a module', array(), get_class($this))
        );

        $products = new \stdClass;
        foreach (['to_configure', 'to_update', 'to_install'] as $subpart) {
            $products->{$subpart} = [];
        }

        foreach ($modulesProvider->getManageModules() as $installed_product) {
            if (!empty($installed_product->warning)) {
                $row = 'to_configure';
            } elseif ($installed_product->installed == 1 && version_compare($installed_product->database_version, $installed_product->version, '<')) {
                $row = 'to_update';
            } else {
                $row = false;
            }

            if ($row) {
                $products->{$row}[] = (object)$installed_product;
            }
        }

        foreach ($modulesProvider->getCatalogModules() as $product) {
            if (isset($product->origin) && $product->origin === 'customer') {
                $products->to_install[] = (object)$product;
            }
        }

        foreach ($products as $product_label => $products_part) {
            $products->$product_label = $this->generateProductUrls($products_part);
        }

        return $this->render('PrestaShopBundle:Admin/Module:notifications.html.twig', array(
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'modules' => $products,
        ));
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

    final private function generateProductUrls(array $products)
    {
        foreach ($products as &$product) {
            $product->urls = [];
            foreach (['install', 'uninstall', 'enable', 'disable', 'reset', 'update'] as $action) {
                $product->urls[$action] = $this->generateUrl('admin_module_manage_action', [
                    'action' => $action,
                    'module_name' => $product->name,
                ]);
            }
            $product->urls['configure'] = $this->generateUrl('admin_module_configure_action', [
                'module_name' => $product->name,
            ]);

            // Which button should be displayed first ?
            $product->url_active = '';
            if (isset($product->installed) && $product->installed == 1) {
                if ($product->active == 0) {
                    $product->url_active = 'enable';
                } elseif ($product->is_configurable == 1) {
                    $product->url_active = 'configure';
                } else {
                    $product->url_active = 'disable';
                }
            } elseif (isset($product->origin) && in_array($product->origin, ['native', 'native_all', 'partner', 'customer'])) {
                $product->url_active = 'install';
            }
        }

        return $products;
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
        //$module = $modulesProvider->getModule($module_name);
        foreach ($modulesProvider->getManageModules() as $module) {
            if ($module->name == $module_name) {
                $old_version = $module->database_version;
                break;
            }
        }

        $modulesProvider->setModuleOnDiskFromAddons($module_name);
        $module = $modulesProvider->getModule($module_name);
        $new_version = $module->database_version;

        $status = version_compare($old_version, $new_version, '>');
        if ($status) {
            $msg = sprintf('Module %s has been updated from %s to %s', $module_name, $old_version, $new_version);
        } else {
            $msg = sprintf('Could not update module %s (Version unchanged)', $module_name);
        }

        return array('status' => $status, 'msg' => $msg);
    }
}
