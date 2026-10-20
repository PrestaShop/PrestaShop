<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class ModuleFrontControllerCore extends FrontController
{
    /** @var Module */
    public $module;

    public function __construct()
    {
        $this->module = Module::getInstanceByName(Tools::getValue('module'));
        if (!($this->module instanceof Module) || !$this->module->active) {
            Tools::redirect('index');

            return;
        }

        $this->page_name = 'module-' . $this->module->name . '-' . Dispatcher::getInstance()->getController();

        parent::__construct();

        $this->controller_type = 'modulefront';
    }

    /**
     * Front controllers redirect to their friendly URL when reached through the parameter form, but
     * that is driven by `php_self`, which a module controller does not have - so module pages were
     * reachable under both URLs with neither pointing at the other.
     *
     * The redirect is limited to requests carrying nothing but the routing parameters. Module
     * controllers are also the payment and callback endpoints, and those arrive with parameters
     * that must not be dropped by a redirect; leaving them alone costs only the canonical URL of
     * pages that were never indexable anyway.
     */
    public function init()
    {
        parent::init();

        if (!$this->shouldRedirectToCanonicalModuleUrl()) {
            return;
        }

        $this->canonicalRedirection($this->context->link->getModuleLink(
            $this->module->name,
            Dispatcher::getInstance()->getController(),
            [],
            $this->ssl,
            $this->context->language->id
        ));
    }

    /**
     * A module controller is also a payment or callback endpoint, and those arrive carrying
     * parameters that a redirect would drop. Only a request that carries nothing but the routing
     * parameters is safe to send to the friendly URL.
     */
    protected function shouldRedirectToCanonicalModuleUrl(): bool
    {
        if (Tools::getValue('ajax') || Tools::isPHPCLI()) {
            return false;
        }

        if ('GET' !== strtoupper($_SERVER['REQUEST_METHOD'] ?? '')) {
            return false;
        }

        return $this->carriesOnlyRoutingParameters($_GET);
    }

    /**
     * True when the query holds nothing but what routed the request, so nothing is lost by sending
     * it to the friendly URL.
     *
     * @param array $query
     */
    protected function carriesOnlyRoutingParameters(array $query): bool
    {
        $routingParameters = ['controller', 'fc', 'module', 'id_lang', 'isolang'];

        return empty(array_diff_key($query, array_flip($routingParameters)));
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

    /**
     * Non-static translation method for frontoffice modules.
     *
     * @deprecated use Context::getContext()->getTranslator()->trans($id, $parameters, $domain, $locale); instead
     *
     * @param string $string Term or expression in english
     * @param false|string $specific Specific name, only for ModuleFrontController
     * @param string|null $class Name of the class
     * @param bool $addslashes If set to true, the return value will pass through addslashes(). Otherwise, stripslashes()
     * @param bool $htmlentities If set to true(default), the return value will pass through htmlentities($string, ENT_QUOTES, 'utf-8')
     *
     * @return string The translation if available, or the english default text
     */
    protected function l($string, $specific = false, $class = null, $addslashes = false, $htmlentities = true)
    {
        return $this->module->l($string, $specific);
    }
}
