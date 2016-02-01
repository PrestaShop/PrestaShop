<?php

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

    public function getIdHook($hook_name)
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
            'name'          => $hook_name,
            'title'         => $title,
            'description'   => $description,
            'position'      => $position
        ]);

        return $this->getIdHook($hook_name);
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
        $id_hook = $this->getIdHook($hook_name);
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
            $id_hook = $this->getIdHook($hook_name);
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
                    $module_name = $key;
                    $extra_data  = $module;
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
        return array_filter(
            $this->getHooksWithModules(),
            [$this->hookInfo, 'isDisplayHookName'],
            ARRAY_FILTER_USE_KEY
        );
    }
}
