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
