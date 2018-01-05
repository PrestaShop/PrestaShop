<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\Core\Module;

use Db;
use Exception;
use Shop;
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;

class HookRepository
{
    private $hookInfo;
    private $shop;
    private $db;
    private $db_prefix;

    public function __construct(
        HookInformationProvider $hookInfo,
        Shop $shop,
        Db $db
    ) {
        $this->hookInfo = $hookInfo;
        $this->shop = $shop;
        $this->db = $db;
        $this->db_prefix = $db->getPrefix();
    }

    public function getIdByName($hook_name)
    {
        $escaped_hook_name = $this->db->escape($hook_name);

        $id_hook = $this->db->getValue(
            "SELECT id_hook FROM {$this->db_prefix}hook WHERE name = '$escaped_hook_name'"
        );

        return (int)$id_hook;
    }

    public function createHook($hook_name, $title = '', $description = '', $position = 1)
    {
        $this->db->insert('hook', [
            'name'          => $this->db->escape($hook_name),
            'title'         => $this->db->escape($title),
            'description'   => $this->db->escape($description),
            'position'      => $this->db->escape($position)
        ], false, true, Db::REPLACE);

        return $this->getIdByName($hook_name);
    }

    private function getIdModule($module_name)
    {
        $escaped_module_name = $this->db->escape($module_name);

        $id_module = $this->db->getValue(
            "SELECT id_module FROM {$this->db_prefix}module WHERE name = '$escaped_module_name'"
        );

        return (int)$id_module;
    }

    public function unHookModulesFromHook($hook_name)
    {
        $id_hook = $this->getIdByName($hook_name);
        $id_shop = (int)$this->shop->id;

        $this->db->execute("DELETE FROM {$this->db_prefix}hook_module
             WHERE id_hook = $id_hook AND id_shop = $id_shop
        ");

        $this->db->execute("DELETE FROM {$this->db_prefix}hook_module_exceptions
            WHERE id_hook = $id_hook AND id_shop = $id_shop
        ");

        return $this;
    }

    /**
     * Saves hook settings for a list of hooks.
     * The $hooks array should have this format:
     * [
     * 		"hookName" => [
     * 			"module1",
     * 			"module2",
     * 			"module3" => [
     * 				"except_pages" => [
     * 					"page1",
     * 					"page2",
     * 					"page3"
     * 				]
     * 			]
     * 		]
     * ]
     * Only hooks present as keys in the $hooks array are affected and all changes
     * are only done for the shop this Repository belongs to.
     */
    public function persistHooksConfiguration(array $hooks)
    {
        $hook_module = [];

        foreach ($hooks as $hook_name => $module_names) {
            $id_hook = $this->getIdByName($hook_name);
            if (!$id_hook) {
                $id_hook = $this->createHook($hook_name);
            }
            if (!$id_hook) {
                throw new Exception(
                    sprintf('Could not create hook `%1$s`.', $hook_name)
                );
            }

            $this->unHookModulesFromHook($hook_name);

            $position = 0;
            foreach ($module_names as $key => $module) {
                if (is_array($module)) {
                    $module_name = key($module);
                    $extra_data  = current($module);
                } else {
                    $module_name = $module;
                    $extra_data  = [];
                }

                ++$position;
                $id_module = $this->getIdModule($module_name);
                if (!$id_module) {
                    continue;
                }

                $row = [
                    'id_module' => $id_module,
                    'id_shop'   => (int)$this->shop->id,
                    'id_hook'   => $id_hook,
                    'position'  => $position
                ];

                $this->db->insert('hook_module', $row);

                if (!empty($extra_data['except_pages'])) {
                    $this->setModuleHookExceptions(
                        $id_module,
                        $id_hook,
                        $extra_data['except_pages']
                    );
                }
            }
        }

        return $this;
    }

    private function setModuleHookExceptions($id_module, $id_hook, array $pages)
    {
        $id_shop    = (int)$this->shop->id;
        $id_module  = (int)$id_module;
        $id_hook    = (int)$id_hook;

        $this->db->execute("DELETE FROM {$this->db_prefix}hook_module_exceptions
            WHERE id_shop = $id_shop
            AND id_module = $id_module
            AND id_hook = $id_hook
        ");

        foreach ($pages as $page) {
            $this->db->insert('hook_module_exceptions', [
                'id_shop'   => $id_shop,
                'id_module' => $id_module,
                'id_hook'   => $id_hook,
                'file_name' => $page
            ]);
        }

        return $this;
    }

    private function getModuleHookExceptions($id_module, $id_hook)
    {
        $id_shop    = (int)$this->shop->id;
        $id_module  = (int)$id_module;
        $id_hook    = (int)$id_hook;

        $rows = $this->db->executeS("SELECT file_name
            FROM {$this->db_prefix}hook_module_exceptions
            WHERE id_shop = $id_shop
            AND id_module = $id_module
            AND id_hook = $id_hook
            ORDER BY file_name ASC
        ");

        return array_map(function ($row) {
            return $row['file_name'];
        }, $rows);
    }

    public function getHooksWithModules()
    {
        $id_shop = (int)$this->shop->id;

        $sql = "SELECT h.name as hook_name, h.id_hook, m.name as module_name, m.id_module
            FROM {$this->db_prefix}hook_module hm
            INNER JOIN {$this->db_prefix}hook h
                ON h.id_hook = hm.id_hook
            INNER JOIN {$this->db_prefix}module m
                ON m.id_module = hm.id_module
            WHERE hm.id_shop = $id_shop
            ORDER BY h.name ASC, hm.position ASC
        ";

        $rows = $this->db->executeS($sql);

        $hooks = [];

        foreach ($rows as $row) {
            $exceptions = $this->getModuleHookExceptions(
                $row['id_module'],
                $row['id_hook']
            );

            if (empty($exceptions)) {
                $hooks[$row['hook_name']][] = $row['module_name'];
            } else {
                $hooks[$row['hook_name']][$row['module_name']] = [
                    'except_pages' => $exceptions
                ];
            }
        }

        return $hooks;
    }

    public function getDisplayHooksWithModules()
    {
        $hooks = [];
        foreach ($this->getHooksWithModules() as $hook_name => $modules) {
            if ($this->hookInfo->isDisplayHookName($hook_name)) {
                $hooks[$hook_name] = $modules;
            }
        }
        return $hooks;
    }
}
