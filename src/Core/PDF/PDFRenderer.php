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

namespace PrestaShop\PrestaShop\Core\PDF;

use Exception;
use TCPDF;

class PDFRenderer extends TCPDF
{
    public const DEFAULT_FONT = 'helvetica';
    public const DEFAULT_FORMAT = 'A4';
    public const DEFAULT_UNIT = 'mm';
    public const DEFAULT_ORIENTATION = 'P';
    public const DEFAULT_ENCODING = 'UTF-8';

    /**
     * Send the file inline to the browser (default).
     * The plug-in is used if available.
     * The name given by name is used when one selects the “Save as” option on the link generating the PDF.
     */
    public const OUTPUT_INLINE = 'I';

    /** Return the document as a string. name is ignored. */
    public const OUTPUT_STRING = 'S';

    /** Send to the browser and force a file download with the name given by name. */
    public const OUTPUT_DOWNLOAD = 'D';

    /** Save to a local server file with the name given by name. */
    public const OUTPUT_FILE = 'F';

    public const AVAILABLE_TYPES = [
        self::OUTPUT_INLINE,
        self::OUTPUT_STRING,
        self::OUTPUT_DOWNLOAD,
        self::OUTPUT_FILE
    ];

    /**
     * @var string
     */
    public string $header;

    /**
     * @var string
     */
    public string $footer;

    /**
     * @var string
     */
    public string $pagination;

    /**
     * @var string
     */
    public string $content;

    /**
     * @var string
     */
    public string $font;

    /**
     * @var array
     */
    public array $fontByLang = [
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
        'hy' => 'freeserif',
    ];

    /**
     *
     * should we use enum structure in PDFRenderer, maybe else?
     *
     * @param bool $useRtl
     * @param string $isoLang
     * @param string $unit
     * @param string $format
     * @param bool $unicode
     * @param string $encoding
     * @param string $orientation
     * @param bool $useCache
     */

    public function __construct(
        bool $useRtl,
        string $isoLang,
        string $unit = self::DEFAULT_UNIT,
        string $format = self::DEFAULT_FORMAT,
        bool $unicode = true,
        string $encoding = self::DEFAULT_ENCODING,
        string $orientation = self::DEFAULT_ORIENTATION,
        bool $useCache = false
    ) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $useCache);
        $this->setRTL($useRtl);
        $this->setFontForLang($isoLang);
    }

    /**
     * set the PDF header.
     *
     * @param string $header HTML
     */
    public function createHeader(string $header): void
    {
        $this->header = $header;
    }

    /**
     * set the PDF footer.
     *
     * @param string $footer HTML
     */
    public function createFooter(string $footer): void
    {
        $this->footer = $footer;
    }

    /**
     * create the PDF content.
     *
     * @param string $content HTML
     */
    public function createContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * create the PDF pagination.
     *
     * @param string $pagination HTML
     */
    public function createPagination(string $pagination): void
    {
        $this->pagination = $pagination;
    }

    /**
     * Change the font.
     *
     * @param string $isoLang
     */
    private function setFontForLang(string $isoLang): void
    {
        if (array_key_exists($isoLang, $this->fontByLang)) {
            $this->font = $this->fontByLang[$isoLang];
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
    public function Header(): void
    {
        $this->writeHTML($this->header);
    }

    /**
     * @see TCPDF::Footer()
     */
    public function Footer(): void
    {
        $this->writeHTML($this->footer);
        $this->FontFamily = self::DEFAULT_FONT;
        $this->writeHTML($this->pagination);
    }

    /**
     * Render HTML template.
     *
     * @param string $filename
     * @param string $display true:display to user, false:save, 'I','D','S' as fpdf display
     *
     * @return string HTML rendered
     */
    public function render(string $filename, string $display = 'D'): string
    {
        $this->lastPage();
        if (in_array($display, self::AVAILABLE_TYPES)) {
            $output = $display;
        } else {
            $output = 'I';
        }

        return $this->Output($filename, $output);
    }

    /**
     * Write a PDF page.
     */
    public function writePage(): void
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
     *
     * @throws Exception
     */
    protected function getRandomSeed(string $seed = ''): string
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
        $seed .= random_int(0, mt_getrandmax());
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

        $seed .= random_int(0, mt_getrandmax());
        $seed .= uniqid('', true);
        $seed .= microtime();

        return $seed;
    }
}
