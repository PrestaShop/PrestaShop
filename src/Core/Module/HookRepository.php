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
        Db $db,
        $db_prefix
    ) {
        $this->hookInfo = $hookInfo;
        $this->shop = $shop;
        $this->db = $db;
        $this->db_prefix = $db_prefix;
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

        return $this;
    }

    public function persistHookConfiguration(array $hooks)
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

            foreach ($module_names as $n => $module_name) {
                $position  = $n + 1;
                $id_module = $this->getIdModule($module_name);
                if (!$id_module) {
                    continue;
                }

                $hook_module[] = [
                    'id_module' => $id_module,
                    'id_shop'   => (int)$this->shop->id,
                    'id_hook'   => $id_hook,
                    'position'  => $position
                ];
            }
        }

        foreach ($hook_module as $row) {
            $this->db->insert('hook_module', $row);
        }

        return $this;
    }

    public function getHooksWithModules()
    {
        $id_shop = (int)$this->shop->id;

        $sql = "SELECT h.name as hook_name, m.name as module_name
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
            $hooks[$row['hook_name']][] = $row['module_name'];
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
