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

namespace PrestaShopBundle\Controller\Api\Improve\Design;

use PrestaShopBundle\Controller\Api\ApiController;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShopBundle\Entity\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Module as LegacyModule;

class PositionsController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request)
    {
        $moduleId = $request->request->getInt('moduleId');
        $hookId = $request->request->getInt('hookId');
        $way = $request->request->getInt('way');
        $positions = $request->request->get('positions');
        $position = (int) is_array($positions) ? array_search($hookId.'_'.$moduleId, $positions) + 1 : null;

        $module = LegacyModule::getInstanceById($moduleId);
        if (empty($module->id)) {
            return $this->jsonResponse(
                [
                    'hasError' => true,
                    'errors' => ['This module cannot be loaded.'],
                ],
                $request
            );
        }

        if (!$module->updatePosition($hookId, $way, $position)) {
            return $this->jsonResponse(
                [
                    'hasError' => true,
                    'errors' => ['Cannot update module position.'],
                ],
                $request
            );
        }

        return $this->jsonResponse([], $request);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getHookableListAction(Request $request)
    {
        if (_PS_MODE_DEMO_) {
            return $this->jsonResponse(
                [
                    'hasError' => true,
                    'errors' => ['Live Edit: This functionality has been disabled.'],
                ],
                $request
            );
        }

        $modules = $request->request->get('modules');
        $hooks = $request->request->get('hooks');
        if (empty($hooks) or empty($modules)) {
            return $this->jsonResponse(
                [
                    'hasError' => true,
                    'errors' => ['Live Edit: no module on this page.'],
                ],
                $request
            );
        }

        $hookableList = [];
        foreach ($modules as $module) {
            $module = trim($module);
            if (empty($module)) {
                continue;
            }

            if (!Validate::isModuleName($module)) {
                return $this->jsonResponse(
                    [
                        'hasError' => true,
                        'errors' => ['Live Edit: module is invalid.'],
                    ],
                    $request
                );
            }

            $moduleInstance = Module::getInstanceByName($module);
            foreach ($hooks as $hookName) {
                $hookName = trim($hookName);
                if (empty($hookName)) {
                    continue;
                }

                if (!isset($hookableList[$hookName])) {
                    $hookableList[$hookName] = [];
                }

                if ($moduleInstance->isHookableOn($hookName)) {
                    $hookableList[$hookName][] = str_replace('_', '-', $module);
                }
            }
        }

        return $this->jsonResponse($hookableList, $request);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getHookableModuleListAction(Request $request)
    {
        if (_PS_MODE_DEMO_) {
            return $this->jsonResponse(
                [
                    'hasError' => true,
                    'errors' => ['Live Edit: This functionality has been disabled.'],
                ],
                $request
            );
        }

        $hookName = $request->request->get('hook');
        $hookableModulesList = [];
        $modules = Db::getInstance()->executeS('SELECT id_module, name FROM `'._DB_PREFIX_.'module` ');
        foreach ($modules as $module) {
            if (!Validate::isModuleName($module['name'])) {
                continue;
            }

            $moduleInstance = Module::getInstanceByName($module);
            if (file_exists(_PS_MODULE_DIR_.$module['name'].'/'.$module['name'].'.php')) {
                include_once(_PS_MODULE_DIR_.$module['name'].'/'.$module['name'].'.php');

                /** @var Module $mod */
                $mod = new $module['name']();
                if ($mod->isHookableOn($hook_name)) {
                    $hookableModulesList[] = array('id' => (int)$mod->id, 'name' => $mod->displayName, 'display' => Hook::exec($hook_name, [], (int)$mod->id));
                }
            }
        }
        die(json_encode($hookableModulesList));

        return $this->jsonResponse($hookableList, $request);
    }
}
