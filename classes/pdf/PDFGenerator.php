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
class PDFGeneratorCore extends TCPDF
{
    const DEFAULT_FONT = 'helvetica';

    /**
     * @var string
     */
    public $header;

    /**
     * @var string
     */
    public $footer;

    /**
     * @var string
     */
    public $pagination;

    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    public $font;

    /**
     * @var array
     */
    public $font_by_lang = [
        'ja' => 'cid0jp',
        'bg' => 'freeserif',
        'ru' => 'freeserif',
        'uk' => 'freeserif',
        'mk' => 'freeserif',
        'el' => 'freeserif',
        'en' => 'dejavusans',
        'vn' => 'dejavusans',
        'pl' => 'dejavusans',
        'ar' => 'dejavusans',
        'fa' => 'dejavusans',
        'ur' => 'dejavusans',
        'az' => 'dejavusans',
        'ca' => 'dejavusans',
        'gl' => 'dejavusans',
        'hr' => 'dejavusans',
        'sr' => 'dejavusans',
        'si' => 'dejavusans',
        'cs' => 'dejavusans',
        'sk' => 'dejavusans',
        'ka' => 'dejavusans',
        'he' => 'dejavusans',
        'lo' => 'dejavusans',
        'lt' => 'dejavusans',
        'lv' => 'dejavusans',
        'tr' => 'dejavusans',
        'ko' => 'cid0kr',
        'zh' => 'cid0cs',
        'tw' => 'cid0cs',
        'th' => 'freeserif',
    ];

    /**
     * @param bool $use_cache
     * @param string $orientation
     */
    public function __construct($use_cache = false, $orientation = 'P')
    {
        parent::__construct($orientation, 'mm', 'A4', true, 'UTF-8', $use_cache, false);
        $this->setRTL(Context::getContext()->language->is_rtl);
    }

    /**
     * set the PDF encoding.
     *
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * set the PDF header.
     *
     * @param string $header HTML
     */
    public function createHeader($header)
    {
        $this->header = $header;
    }

    /**
     * set the PDF footer.
     *
     * @param string $footer HTML
     */
    public function createFooter($footer)
    {
        $this->footer = $footer;
    }

    /**
     * create the PDF content.
     *
     * @param string $content HTML
     */
    public function createContent($content)
    {
        $this->content = $content;
    }

    /**
     * create the PDF pagination.
     *
     * @param string $pagination HTML
     */
    public function createPagination($pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * Change the font.
     *
     * @param string $iso_lang
     */
    public function setFontForLang($iso_lang)
    {
        if (array_key_exists($iso_lang, $this->font_by_lang)) {
            $this->font = $this->font_by_lang[$iso_lang];
        } else {
            $this->font = self::DEFAULT_FONT;
        }

        $this->setHeaderFont([$this->font, '', PDF_FONT_SIZE_MAIN, '', false]);
        $this->setFooterFont([$this->font, '', PDF_FONT_SIZE_MAIN, '', false]);

        $this->setFont($this->font, '', PDF_FONT_SIZE_MAIN, '', false);
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
        $this->FontFamily = self::DEFAULT_FONT;
        $this->writeHTML($this->pagination);
    }

    /**
     * Render HTML template.
     *
     * @param string $filename
     * @param bool $display true:display to user, false:save, 'I','D','S' as fpdf display
     *
     * @throws PrestaShopException
     *
     * @return string HTML rendered
     */
    public function render($filename, $display = true)
    {
        if (empty($filename)) {
            throw new PrestaShopException('Missing filename.');
        }

        $this->lastPage();

        if ($display === true) {
            $output = 'D';
        } elseif ($display === false) {
            $output = 'S';
        } elseif ($display == 'D') {
            $output = 'D';
        } elseif ($display == 'S') {
            $output = 'S';
        } elseif ($display == 'F') {
            $output = 'F';
        } else {
            $output = 'I';
        }

        return $this->Output($filename, $output);
    }

    /**
     * Write a PDF page.
     */
    public function writePage()
    {
        $this->SetHeaderMargin(5);
        $this->SetFooterMargin(21);
        $this->setMargins(10, 40, 10);
        $this->AddPage();
        $this->writeHTML($this->content, true, false, true, false, '');
    }

    /**
     * Override of TCPDF::getRandomSeed() - getmypid() is blocked on several hosting.
     *
     * @param string $seed
     *
     * @return string
     */
    protected function getRandomSeed($seed = '')
    {
        $seed .= microtime();

        if (function_exists('openssl_random_pseudo_bytes') && (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) {
            // this is not used on windows systems because it is very slow for a know bug
            $seed .= openssl_random_pseudo_bytes(512);
        } else {
            for ($i = 0; $i < 23; ++$i) {
                $seed .= uniqid('', true);
            }
        }

        $seed .= uniqid('', true);
        $seed .= mt_rand(0, mt_getrandmax());
        $seed .= __FILE__;
        $seed .= $this->bufferlen;

        if (isset($_SERVER['REMOTE_ADDR'])) {
            $seed .= $_SERVER['REMOTE_ADDR'];
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $seed .= $_SERVER['HTTP_USER_AGENT'];
        }
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            $seed .= $_SERVER['HTTP_ACCEPT'];
        }
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            $seed .= $_SERVER['HTTP_ACCEPT_ENCODING'];
        }
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $seed .= $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }
        if (isset($_SERVER['HTTP_ACCEPT_CHARSET'])) {
            $seed .= $_SERVER['HTTP_ACCEPT_CHARSET'];
        }

        $seed .= mt_rand(0, mt_getrandmax());
        $seed .= uniqid('', true);
        $seed .= microtime();

        return $seed;
    }
}
