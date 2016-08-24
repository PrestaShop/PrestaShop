<?php

abstract class PdfFrontControllerCore extends FrontController
{
    public function init()
    {
        parent::init();

        /* For PDF we restore some functions from Smarty
         * they've been removed in PrestaShop 1.7 so
         * new themes don't use them. Although PDF haven't been
         * reworked so every PDF controller must extend this class.
         */
        smartyRegisterFunction($this->context->smarty, 'function', 'convertPrice', array('Product', 'convertPrice'));
        smartyRegisterFunction($this->context->smarty, 'function', 'convertPriceWithCurrency', array('Product', 'convertPriceWithCurrency'));
        smartyRegisterFunction($this->context->smarty, 'function', 'displayWtPrice', array('Product', 'displayWtPrice'));
        smartyRegisterFunction($this->context->smarty, 'function', 'displayWtPriceWithCurrency', array('Product', 'displayWtPriceWithCurrency'));
        smartyRegisterFunction($this->context->smarty, 'function', 'displayPrice', array('Tools', 'displayPriceSmarty'));
        smartyRegisterFunction($this->context->smarty, 'modifier', 'convertAndFormatPrice', array('Product', 'convertAndFormatPrice')); // used twice
        smartyRegisterFunction($this->context->smarty, 'function', 'displayAddressDetail', array('AddressFormat', 'generateAddressSmarty'));
        smartyRegisterFunction($this->context->smarty, 'function', 'getWidthSize', array('Image', 'getWidth'));
        smartyRegisterFunction($this->context->smarty, 'function', 'getHeightSize', array('Image', 'getHeight'));
    }
}
