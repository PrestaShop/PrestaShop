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

namespace PrestaShopBundle\Service\Improve\Design;

use PrestaShopBundle\Exception;
use PrestaShop\PrestaShop\Core\Validation\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

class PositionsService
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Get Hookable list from request
     *
     * @param Request $request Request
     *
     * @return array
     *
     * @throw Exception\HookModuleNotFoundException|Exception\InvalidModuleException
     */
    public function getHookableList(Request $request)
    {
        $modules = $request->request->get('modules');
        $hooks = $request->request->get('hooks');

        if (empty($hooks) or empty($modules)) {
            throw new Exception\HookModuleNotFoundException();
        }

        $hookableList = [];
        foreach ($modules as $module) {
            $module = trim($module);
            if (empty($module)) {
                continue;
            }

            if (!$this->validator->isModuleName($module)) {
                throw new Exception\InvalidModuleException();
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

        return $hookableList;
    }

    /**
     * Get Hookable module list from request
     *
     * @param Request $request Request
     *
     * @return array
     */
    public function getHookableModuleList(Request $request)
    {
        $hookName = $request->request->get('hook');
        $hookableModulesList = [];
        $modules = Db::getInstance()->executeS('SELECT id_module, name FROM `'._DB_PREFIX_.'module` ');
        foreach ($modules as $module) {
            if (!$this->validator->isModuleName($module['name'])) {
                continue;
            }

            $moduleInstance = Module::getInstanceByName($module);
            if (file_exists(_PS_MODULE_DIR_.$module['name'].'/'.$module['name'].'.php')) {
                include_once(_PS_MODULE_DIR_.$module['name'].'/'.$module['name'].'.php');

                /** @var Module $mod */
                $mod = new $module['name']();
                if ($mod->isHookableOn($hookName)) {
                    $hookableModulesList[] = [
                        'id' => (int)$mod->id,
                        'name' => $mod->displayName,
                        'display' => Hook::exec($hookName, [], (int)$mod->id)
                    ];
                }
            }
        }

        return $hookableModulesList;
    }
}
