<?php

require_once(_PS_TOOL_DIR_.'tcpdf/config/lang/eng.php');
require_once(_PS_TOOL_DIR_.'tcpdf/tcpdf.php');

/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 8797 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5
 */
class PDFGeneratorCore extends TCPDF
{
	const DEFAULT_FONT = 'helvetica';

	public $header;
	public $footer;
	public $content;
	public $font;

	public $font_by_lang = array('jp' => 'cid0jp');


	public function __construct($use_cache = false)
	{
		parent::__construct('P', 'mm', 'A4', true, 'UTF-8', $use_cache, false);
	}

	/**
	 * set the PDF encoding
	 * @param string $encoding
	 */
	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
	}

	/**
	 *
	 * set the PDF header
	 * @param string $header HTML
	 */
	public function createHeader($header)
	{
		$this->header = $header;
	}

	/**
	 *
	 * set the PDF footer
	 * @param string $footer HTML
	 */
	public function createFooter($footer)
	{
		$this->footer = $footer;
	}

	/**
	 *
	 * create the PDF content
	 * @param string $content HTML
	 */
	public function createContent($content)
	{
		$this->content = $content;
	}

	/**
	 * Change the font
	 * @param string $iso_lang
	 */
	public function setFontForLang($iso_lang)
	{
		$this->font = PDFGenerator::DEFAULT_FONT;
		$this->setHeaderFont(array(PDFGenerator::DEFAULT_FONT, '', PDF_FONT_SIZE_MAIN));
		$this->setFooterFont(array(PDFGenerator::DEFAULT_FONT, '', PDF_FONT_SIZE_MAIN));
		if (array_key_exists($iso_lang, $this->font_by_lang))
			$this->font = $this->font_by_lang[$iso_lang];

		$this->setFont($this->font);
	}

	/**
	 * @see TCPDF::Header()
	 */
	public function Header()
	{
		$this->writeHTML($this->header);
	}

	/**
	 * @see TCPDF::Footer()
	 */
	public function Footer()
	{
		$this->writeHTML($this->footer);
	}

	/**
	 * Render the pdf file
	 *
	 * @param string $filename
     * @param boolean $inline
	 * @throws PrestaShopException
	 */
	public function render($filename, $display = true)
	{
		if (empty($filename))
			throw new PrestaShopException('Missing filename.');

		$this->lastPage();

		$output = $display ? 'I' : 'S';
		return $this->output($filename, $output);
	}

	/**
	 * Write a PDF page
	 */
	public function writePage()
	{
		$this->SetHeaderMargin(5);
		$this->SetFooterMargin(18);
		$this->setMargins(10, 40, 10);
		$this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

		$this->AddPage();

		$this->writeHTML($this->content, true, false, true, false, '');
	}
}