<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
abstract class TaxManagerModuleCore extends Module
{
    public $tax_manager_class;

    public function install()
    {
        return parent::install() && $this->registerHook('taxManager');
    }

    public function hookTaxManager($args)
    {
        $class_file = _PS_MODULE_DIR_ . '/' . $this->name . '/' . $this->tax_manager_class . '.php';

        if (!isset($this->tax_manager_class) || !file_exists($class_file)) {
            die($this->trans('Incorrect Tax Manager class [%s]', [htmlspecialchars($this->tax_manager_class)], 'Admin.International.Notification'));
        }

        require_once $class_file;

        if (!class_exists($this->tax_manager_class)) {
            die($this->trans('Tax Manager class not found [%s]', [htmlspecialchars($this->tax_manager_class)], 'Admin.International.Notification'));
        }

        $class = $this->tax_manager_class;
        if (call_user_func([$class, 'isAvailableForThisAddress'], $args['address'])) {
            return new $class();
        }

        return false;
    }
}
