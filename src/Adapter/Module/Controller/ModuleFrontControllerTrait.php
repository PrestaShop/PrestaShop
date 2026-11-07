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

namespace PrestaShop\PrestaShop\Adapter\Module\Controller;

use Cart;
use Configuration;
use Currency;
use Dispatcher;
use Hook;
use Module;
use PrestaShopException;
use Tools;

/**
 * Trait ModuleFrontControllerTrait centralizes the common behavior shared by
 * `ModuleFrontControllerCore` and `ModuleProductListingFrontControllerCore`.
 */
trait ModuleFrontControllerTrait
{
    /** @var Module */
    public $module;

    protected function initializeModuleFromRequest(): void
    {
        $moduleName = (string) Tools::getValue('module');
        $this->module = Module::getInstanceByName($moduleName);

        if (!($this->module instanceof Module) || !$this->module->active) {
            Tools::redirect('index');
        }

        $this->page_name = 'module-' . $this->module->name . '-' . Dispatcher::getInstance()->getController();
        $this->controller_type = 'modulefront';
    }

    /**
     * Assigns module template for page content.
     *
     * @param string $template Template filename
     *
     * @throws PrestaShopException
     */
    public function setTemplate($template, $params = [], $locale = null)
    {
        if (strpos($template, 'module:') === 0) {
            $this->template = $template;
        } else {
            parent::setTemplate($template, $params, $locale);
        }
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if (Tools::isSubmit('module') && Tools::getValue('controller') == 'payment') {
            $currency = Currency::getCurrency((int) $this->context->cart->id_currency);
            $minimalPurchase = Tools::convertPrice((float) Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
            Hook::exec('overrideMinimalPurchasePrice', [
                'minimalPurchase' => &$minimalPurchase,
            ]);
            if ($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimalPurchase) {
                Tools::redirect($this->context->link->getPageLink(
                    'order',
                    null,
                    null,
                    ['step' => 1]
                ));
            }
        }
        parent::initContent();
    }

    protected function l($string, $specific = false, $class = null, $addslashes = false, $htmlentities = true)
    {
        return $this->module ? $this->module->l($string, $specific) : $string;
    }
}
