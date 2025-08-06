<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF\Engine;

use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\PDF\PDFRendererEngineInterface;
use TCPDF;

/**
 * TCPDF-backed implementation of PDFRendererEngineInterface.
 *
 * Not autowired as a service: it is single-use and stateful (one instance per
 * PDF render), so it is instantiated by {@see TCPDFRendererEngineFactory}.
 *
 * @see \PDFGeneratorCore the legacy equivalent this replaces
 */
class PDFRendererTCPDF extends TCPDF implements PDFRendererEngineInterface
{
    public const DEFAULT_FONT = 'helvetica';

    /**
     * Same mapping as the legacy PDFGeneratorCore, kept identical so existing
     * PDFs keep rendering with the same font per language.
     *
     * @var array<string, string>
     */
    private const FONT_BY_LANG = [
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
        'ro' => 'dejavusans',
        'ko' => 'cid0kr',
        'zh' => 'cid0cs',
        'tw' => 'cid0cs',
        'th' => 'freeserif',
        'hy' => 'freeserif',
    ];

    private string $header = '';

    private string $footer = '';

    private string $pagination = '';

    private string $content = '';

    public function __construct(bool $useCache, string $orientation, bool $rtl)
    {
        parent::__construct($orientation, 'mm', 'A4', true, 'UTF-8', $useCache, false);
        $this->setRTL($rtl);
    }

    public function setFontForLanguage(string $isoCode): void
    {
        $font = self::FONT_BY_LANG[$isoCode] ?? self::DEFAULT_FONT;

        $this->setHeaderFont([$font, '', PDF_FONT_SIZE_MAIN, '', false]);
        $this->setFooterFont([$font, '', PDF_FONT_SIZE_MAIN, '', false]);
        $this->setFont($font, '', PDF_FONT_SIZE_MAIN, '', false);
    }

    public function startNewPageGroup(): void
    {
        $this->startPageGroup();
    }

    public function createHeader(string $header): void
    {
        $this->header = $header;
    }

    public function createFooter(string $footer): void
    {
        $this->footer = $footer;
    }

    public function createContent(string $content): void
    {
        $this->content = $content;
    }

    public function createPagination(string $pagination): void
    {
        $this->pagination = $pagination;
    }

    public function writePage(): void
    {
        $this->SetHeaderMargin(5);
        $this->SetFooterMargin(21);
        $this->setMargins(10, 40, 10);
        $this->AddPage();
        $this->writeHTML($this->content, true, false, true, false, '');
    }

    public function outputPdf(string $filename, bool $display): string
    {
        if (empty($filename)) {
            throw new CoreException('Missing filename.');
        }

        $this->lastPage();

        return (string) $this->Output($filename, $display ? 'D' : 'S');
    }

    /**
     * @see TCPDF::Header()
     */
    public function Header() // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    {
        $this->writeHTML($this->header);
    }

    /**
     * @see TCPDF::Footer()
     */
    public function Footer() // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    {
        $this->writeHTML($this->footer);
        $this->FontFamily = self::DEFAULT_FONT;
        $this->writeHTML($this->pagination);
    }

    /**
     * Override of TCPDF::getRandomSeed() - getmypid() is blocked on several hosting.
     * Identical to the legacy PDFGeneratorCore override.
     *
     * @param string $seed
     *
     * @return string
     */
    protected function getRandomSeed($seed = '')
    {
        $seed .= microtime();

        if (function_exists('openssl_random_pseudo_bytes') && (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) {
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
