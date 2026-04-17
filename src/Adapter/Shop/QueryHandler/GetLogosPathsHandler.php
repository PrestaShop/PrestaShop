<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shop\QueryHandler;

use Configuration;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetLogosPaths;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryHandler\GetLogosPathsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\LogosPaths;

/**
 * Responsible for providing header, email, invoice and favicon logo paths for specific shop context.
 */
#[AsQueryHandler]
final class GetLogosPathsHandler implements GetLogosPathsHandlerInterface
{
    /**
     * @var string
     */
    private $imageBaseUrl;

    /**
     * @var string
     */
    private $imageDirectory;

    /**
     * @param string $imageBaseUrl
     * @param string $imageDirectory
     */
    public function __construct($imageBaseUrl, $imageDirectory)
    {
        $this->imageBaseUrl = $imageBaseUrl;
        $this->imageDirectory = $imageDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetLogosPaths $query)
    {
        return new LogosPaths(
            $this->getHeaderLogoPath(),
            $this->getMailLogoPath(),
            $this->getInvoiceLogoPath(),
            $this->getFaviconPath()
        );
    }

    /**
     * Get path to context's shop logo.
     *
     * @return string
     */
    private function getHeaderLogoPath()
    {
        return $this->imageBaseUrl . Configuration::get('PS_LOGO');
    }

    /**
     * Get path to context's shop mail logo.
     *
     * @return string
     */
    private function getMailLogoPath()
    {
        if (!$mailLogo = Configuration::get('PS_LOGO_MAIL')) {
            return $this->getHeaderLogoPath();
        }

        $mailLogoPath = $this->imageDirectory . $mailLogo;

        if (!file_exists($mailLogoPath)) {
            return $this->getHeaderLogoPath();
        }

        return $this->imageBaseUrl . $mailLogo;
    }

    /**
     * Get path to context's shop invoice logo.
     *
     * @return string
     */
    private function getInvoiceLogoPath()
    {
        if (!$invoiceLogo = Configuration::get('PS_LOGO_INVOICE')) {
            return $this->getHeaderLogoPath();
        }

        $invoiceLogoPath = $this->imageDirectory . $invoiceLogo;

        if (!file_exists($invoiceLogoPath)) {
            return $this->getHeaderLogoPath();
        }

        return $this->imageBaseUrl . $invoiceLogo;
    }

    /**
     * Get path to context's shop favicon.
     *
     * @return string
     */
    private function getFaviconPath()
    {
        return $this->imageBaseUrl . Configuration::get('PS_FAVICON');
    }
}
