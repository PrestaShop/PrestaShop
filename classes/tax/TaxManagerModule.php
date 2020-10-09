<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
            die($this->trans('Incorrect Tax Manager class [%s]', [$this->tax_manager_class], 'Admin.International.Notification'));
        }

        require_once $class_file;

        if (!class_exists($this->tax_manager_class)) {
            die($this->trans('Tax Manager class not found [%s]', [$this->tax_manager_class], 'Admin.International.Notification'));
        }

        $class = $this->tax_manager_class;
        if (call_user_func([$class, 'isAvailableForThisAddress'], $args['address'])) {
            return new $class();
        }

        return false;
    }
}
