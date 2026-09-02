<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult;

/**
 * Holds data of header, email, invoice and favicon logos paths
 */
class LogosPaths
{
    /**
     * @var string
     */
    private $headerLogoPath;

    /**
     * @var string
     */
    private $mailLogoPath;

    /**
     * @var string
     */
    private $invoiceLogoPath;

    /**
     * @var string
     */
    private $faviconPath;

    /**
     * @param string $headerLogoPath
     * @param string $mailLogoPath
     * @param string $invoiceLogoPath
     * @param string $faviconPath
     */
    public function __construct(
        $headerLogoPath,
        $mailLogoPath,
        $invoiceLogoPath,
        $faviconPath
    ) {
        $this->headerLogoPath = $headerLogoPath;
        $this->mailLogoPath = $mailLogoPath;
        $this->invoiceLogoPath = $invoiceLogoPath;
        $this->faviconPath = $faviconPath;
    }

    /**
     * @return string
     */
    public function getHeaderLogoPath()
    {
        return $this->headerLogoPath;
    }

    /**
     * @return string
     */
    public function getMailLogoPath()
    {
        return $this->mailLogoPath;
    }

    /**
     * @return string
     */
    public function getInvoiceLogoPath()
    {
        return $this->invoiceLogoPath;
    }

    /**
     * @return string
     */
    public function getFaviconPath()
    {
        return $this->faviconPath;
    }
}
