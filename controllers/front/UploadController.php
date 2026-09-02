<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    public function init(): void
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

    public function postProcess(): void
    {
        $this->sendFile($this->getPath(), $this->filename, false);
    }

    private function getPath(): string
    {
        return _PS_UPLOAD_DIR_ . basename($this->filename);
    }
}
