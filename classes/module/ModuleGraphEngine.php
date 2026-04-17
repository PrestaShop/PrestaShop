<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
abstract class ModuleGraphEngineCore extends Module
{
    protected $_type;

    public function __construct($type)
    {
        $this->_type = $type;
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        return Configuration::updateValue('PS_STATS_RENDER', $this->name);
    }

    public static function getGraphEngines()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT m.`name`
            FROM `' . _DB_PREFIX_ . 'module` m
            LEFT JOIN `' . _DB_PREFIX_ . 'hook_module` hm ON hm.`id_module` = m.`id_module`
            LEFT JOIN `' . _DB_PREFIX_ . 'hook` h ON hm.`id_hook` = h.`id_hook`
            WHERE h.`name` = \'displayAdminStatsGraphEngine\'
        ');

        $array_engines = [];
        foreach ($result as $module) {
            $instance = Module::getInstanceByName($module['name']);
            if (!$instance) {
                continue;
            }
            $array_engines[$module['name']] = [$instance->displayName, $instance->description];
        }

        return $array_engines;
    }

    abstract public function createValues($values);

    abstract public function setSize($width, $height);

    abstract public function setLegend($legend);

    abstract public function setTitles($titles);

    abstract public function draw();
}
