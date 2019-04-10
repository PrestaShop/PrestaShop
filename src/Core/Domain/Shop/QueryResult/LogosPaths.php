<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
