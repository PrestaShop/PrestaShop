<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * @deprecated since 9.0 and will be removed in 10.0, stock is now managed by new logic
 */
abstract class StockManagerModuleCore extends Module
{
    public $stock_manager_class;

    public function install()
    {
        return parent::install() && $this->registerHook('stockManager');
    }

    public function hookStockManager()
    {
        $class_file = _PS_MODULE_DIR_ . '/' . $this->name . '/' . $this->stock_manager_class . '.php';

        if (!isset($this->stock_manager_class) || !file_exists($class_file)) {
            die($this->trans('Incorrect Stock Manager class [%s]', [htmlspecialchars($this->stock_manager_class)], 'Admin.Catalog.Notification'));
        }

        require_once $class_file;

        if (!class_exists($this->stock_manager_class)) {
            die($this->trans('Stock Manager class not found [%s]', [htmlspecialchars($this->stock_manager_class)], 'Admin.Catalog.Notification'));
        }

        $class = $this->stock_manager_class;
        if (call_user_func([$class, 'isAvailable'])) {
            return new $class();
        }

        return false;
    }
}
