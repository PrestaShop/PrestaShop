<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class ChangeCurrencyControllerCore extends FrontController
{
    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        $currency = new Currency((int) Tools::getValue('id_currency'));
        if (Validate::isLoadedObject($currency) && !$currency->deleted) {
            $this->context->cookie->id_currency = (int) $currency->id;
            $this->ajaxRender('1');

            return;
        }
        $this->ajaxRender('0');
    }
}
