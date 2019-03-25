<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Theme\QueryHandler;


use Configuration;
use PrestaShop\PrestaShop\Core\Domain\Theme\Query\GetThemeLogos;
use PrestaShop\PrestaShop\Core\Domain\Theme\QueryHandler\GetLogoHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Theme\QueryResult\Logo;

/**
 * Responsible for providing header, email, invoice and favicon logo paths for specific shop context.
 */
final class GetThemeLogosHandler implements GetLogoHandlerInterface
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
    public function handle(GetThemeLogos $query)
    {
        return new Logo(
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
