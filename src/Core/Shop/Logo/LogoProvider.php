<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Shop\Logo;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class LogoProvider provides logos for context shop.
 */
final class LogoProvider implements LogoProviderInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLogo()
    {
        return $this->configuration->get('_PS_IMG_') . $this->configuration->get('PS_LOGO');
    }

    /**
     * {@inheritdoc}
     */
    public function getMailLogoPath()
    {
        if (!$mailLogo = $this->configuration->get('PS_LOGO_MAIL')) {
            return $this->getHeaderLogo();
        }

        $mailLogoPath = $this->configuration->get('_PS_IMG_DIR_') . $mailLogo;

        if (!file_exists($mailLogoPath)) {
            return $this->getHeaderLogo();
        }

        return $this->configuration->get('_PS_IMG_') . $mailLogo;
    }

    /**
     * {@inheritdoc}
     */
    public function getInvoiceLogoPath()
    {
        if (!$invoiceLogo = $this->configuration->get('PS_LOGO_INVOICE')) {
            return $this->getHeaderLogo();
        }

        $invoiceLogoPath = $this->configuration->get('_PS_IMG_DIR_') . $invoiceLogo;

        if (!file_exists($invoiceLogoPath)) {
            return $this->getHeaderLogo();
        }

        return $this->configuration->get('_PS_IMG_') . $invoiceLogo;
    }

    /**
     * {@inheritdoc}
     */
    public function getFaviconPath()
    {
        return $this->configuration->get('_PS_IMG_') . $this->configuration->get('PS_FAVICON');
    }
}
