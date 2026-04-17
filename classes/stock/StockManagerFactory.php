<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * StockManagerFactory : factory of stock manager
 *
 * @deprecated since 9.0 and will be removed in 10.0, stock is now managed by new logic
 */
class StockManagerFactoryCore
{
    /**
     * @var StockManagerInterface|null Instance of the current StockManager
     */
    protected static $stock_manager;

    /**
     * Returns a StockManager.
     *
     * @return StockManagerInterface
     */
    public static function getManager()
    {
        if (!isset(StockManagerFactory::$stock_manager)) {
            $stock_manager = StockManagerFactory::execHookStockManagerFactory();
            if (!($stock_manager instanceof StockManagerInterface)) {
                $stock_manager = new StockManager();
            }
            StockManagerFactory::$stock_manager = $stock_manager;
        }

        return StockManagerFactory::$stock_manager;
    }

    /**
     *  Looks for a StockManager in the modules list.
     *
     * @return StockManagerInterface
     */
    public static function execHookStockManagerFactory()
    {
        $modules_infos = Hook::getModulesFromHook(Hook::getIdByName('stockManager'));
        $stock_manager = false;

        foreach ($modules_infos as $module_infos) {
            $module_instance = Module::getInstanceByName($module_infos['name']);

            if (is_callable([$module_instance, 'hookStockManager'])) {
                $stock_manager = $module_instance->hookStockManager();
            }

            if ($stock_manager) {
                break;
            }
        }

        return $stock_manager;
    }
}
