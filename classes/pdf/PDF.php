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

/**
 * @since 1.5
 */
class PDFCore
{
    /**
     * @var string
     */
    public $filename;

    /**
     * @var PDFGenerator
     */
    public $pdf_renderer;

    /**
     * @var PrestaShopCollection|ObjectModel|array
     */
    public $objects;

    /**
     * @var string
     */
    public $template;

    /**
     * @var bool
     */
    public $send_bulk_flag = false;

    /**
     * @var Smarty
     */
    protected $smarty;

    public const TEMPLATE_INVOICE = 'Invoice';
    public const TEMPLATE_ORDER_RETURN = 'OrderReturn';
    public const TEMPLATE_ORDER_SLIP = 'OrderSlip';
    public const TEMPLATE_DELIVERY_SLIP = 'DeliverySlip';
    public const TEMPLATE_SUPPLY_ORDER_FORM = 'SupplyOrderForm';

    /**
     * @param PrestaShopCollection|ObjectModel|array $objects
     * @param string $template
     * @param Smarty $smarty
     * @param string $orientation
     */
    public function __construct($objects, $template, $smarty, $orientation = 'P')
    {
        $this->pdf_renderer = new PDFGenerator((bool) Configuration::get('PS_PDF_USE_CACHE'), $orientation);
        $this->template = $template;

        /*
         * We need a Smarty instance that does NOT escape HTML.
         * Since in BO Smarty does not autoescape
         * and in FO Smarty does autoescape, we use
         * a new Smarty of which we're sure it does not escape
         * the HTML.
         */
        $this->smarty = clone $smarty;
        $this->smarty->escape_html = false;

        /* We need to get the old instance of the LazyRegister
         * because some of the functions are already defined
         * and we need to check in the old one first
         */
        $original_lazy_register = SmartyLazyRegister::getInstance($smarty);

        /* For PDF we restore some functions from Smarty
         * they've been removed in PrestaShop 1.7 so
         * new themes don't use them. Although PDF haven't been
         * reworked so every PDF controller must extend this class.
         */
        smartyRegisterFunction($this->smarty, 'function', 'convertPrice', ['Product', 'convertPrice'], true, $original_lazy_register);
        smartyRegisterFunction($this->smarty, 'function', 'convertPriceWithCurrency', ['Product', 'convertPriceWithCurrency'], true, $original_lazy_register);
        smartyRegisterFunction($this->smarty, 'function', 'displayWtPrice', ['Product', 'displayWtPrice'], true, $original_lazy_register);
        smartyRegisterFunction($this->smarty, 'function', 'displayWtPriceWithCurrency', ['Product', 'displayWtPriceWithCurrency'], true, $original_lazy_register);
        smartyRegisterFunction($this->smarty, 'function', 'displayPrice', ['Tools', 'displayPriceSmarty'], true, $original_lazy_register);
        smartyRegisterFunction($this->smarty, 'modifier', 'convertAndFormatPrice', ['Product', 'convertAndFormatPrice'], true, $original_lazy_register); // used twice
        smartyRegisterFunction($this->smarty, 'function', 'displayAddressDetail', ['AddressFormat', 'generateAddressSmarty'], true, $original_lazy_register);
        smartyRegisterFunction($this->smarty, 'function', 'getWidthSize', ['Image', 'getWidth'], true, $original_lazy_register);
        smartyRegisterFunction($this->smarty, 'function', 'getHeightSize', ['Image', 'getHeight'], true, $original_lazy_register);

        $this->objects = $objects;
        if (!($objects instanceof Iterator) && !is_array($objects)) {
            $this->objects = [$objects];
        }

        if (count($this->objects) > 1) { // when bulk mode only
            $this->send_bulk_flag = true;
        }

        $this->setFilename();
    }

    /**
     * Render PDF.
     *
     * @param bool $display
     *
     * @return string|void
     *
     * @throws PrestaShopException
     */
    public function render($display = true)
    {
        $render = false;
        $this->pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
        foreach ($this->objects as $object) {
            $this->pdf_renderer->startPageGroup();
            $template = $this->getTemplateObject($object);
            if (!$template) {
                continue;
            }

            $template->assignHookData($object);

            $this->pdf_renderer->createHeader($template->getHeader());
            $this->pdf_renderer->createPagination($template->getPagination());
            $this->pdf_renderer->createContent($template->getContent());
            $this->pdf_renderer->writePage();
            // The footer must be added after adding the page, or TCPDF will
            // add the footer for the next page from on the last page of this
            // page group, which could mean the wrong store info is rendered.
            $this->pdf_renderer->createFooter($template->getFooter());
            $render = true;

            unset($template);
        }

        if ($render) {
            // clean the output buffer
            if (ob_get_level() && ob_get_length() > 0) {
                ob_clean();
            }

            return $this->pdf_renderer->render($this->getFilename(), $display);
        }
    }

    /**
     * Get correct PDF template classes.
     *
     * @param mixed $object
     *
     * @return HTMLTemplate|false
     *
     * @throws PrestaShopException
     */
    public function getTemplateObject($object)
    {
        $class = false;
        $class_name = 'HTMLTemplate' . $this->template;

        if (class_exists($class_name)) {
            // Some HTMLTemplateXYZ implementations won't use the third param but this is not a problem (no warning in PHP),
            // the third param is then ignored if not added to the method signature.
            $class = new $class_name($object, $this->smarty, $this->send_bulk_flag);

            if (!($class instanceof HTMLTemplate)) {
                throw new PrestaShopException('Invalid class. It should be an instance of HTMLTemplate');
            }
        }

        return $class;
    }

    /**
     * Get the PDF filename based on the objects.
     *
     * @return string
     */
    public function getFilename(): string
    {
        if (empty($this->filename)) {
            $this->setFilename();
        }

        return $this->filename;
    }

    /**
     * Set the PDF filename based on the objects.
     *
     * @return bool
     */
    public function setFilename(): bool
    {
        $bulk = (1 < count($this->objects));

        foreach ($this->objects as $object) {
            $template = $this->getTemplateObject($object);
            if (!$template) {
                continue;
            }

            if ($bulk) {
                $this->filename = $template->getBulkFilename();
            } else {
                $this->filename = $template->getFilename();
            }

            if (!empty($this->filename)) {
                break;
            }
        }

        return !empty($this->filename);
    }
}
