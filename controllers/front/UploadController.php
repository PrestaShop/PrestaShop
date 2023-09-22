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

declare(strict_types=1);

class UploadControllerCore extends GetFileController
{
    private $filename;

    /**
     * Initialize the controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        FrontController::init();
        if (Tools::getValue('file') !== null) {
            $this->filename = pSQL(Tools::getValue('file'));
        }

        if (!file_exists($this->getPath()) || (!$this->isCustomization() && !$this->isEmployee())) {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    private function isEmployee(): bool
    {
        return !empty((new Cookie('psAdmin'))->id_employee);
    }

    private function isCustomization(): bool
    {
        if ($this->filename === null || !($this->context->cart instanceof Cart)) {
            return false;
        }

        $isCustomization = Db::getInstance()->getValue('SELECT 1
            FROM ' . _DB_PREFIX_ . 'cart c
            INNER JOIN ' . _DB_PREFIX_ . 'customization cu ON c.id_cart = cu.id_cart
            INNER JOIN ' . _DB_PREFIX_ . 'customized_data cd ON cd.id_customization = cu.id_customization
            LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON c.id_cart = o.id_cart
            WHERE (c.id_customer = ' . (int) $this->context->cart->id_customer . '
            AND c.id_guest = ' . (int) $this->context->cart->id_guest . '
            OR o.reference = "' . pSQL(Tools::getValue('reference')) . '")
            AND cd.type = ' . Product::CUSTOMIZE_FILE . '
            AND (cd.value = "' . $this->filename . '" OR CONCAT(cd.value, "_small") = "' . $this->filename . '")');

        return (bool) $isCustomization;
    }

    public function postProcess()
    {
        $this->sendFile($this->getPath(), $this->filename, false);
    }

    private function getPath(): string
    {
        return _PS_UPLOAD_DIR_ . $this->filename;
    }
}
