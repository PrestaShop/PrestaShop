<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\System;

use PrestaShop\PrestaShop\Adapter\Hosting\HostingInformation;
use PrestaShop\PrestaShop\Adapter\Mail\MailingInformation;
use PrestaShop\PrestaShop\Adapter\Shop\ShopInformation;

/**
 * Provides a summary of system information.
 */
class SystemInformation
{
    /**
     * @var HostingInformation
     */
    private $hostingInformation;

    /**
     * @var MailingInformation
     */
    private $mailingInformation;

    /**
     * @var ShopInformation
     */
    private $shopInformation;

    public function __construct(
        HostingInformation $hostingInformation,
        MailingInformation $mailingInformation,
        ShopInformation $shopInformation
    ) {
        $this->hostingInformation = $hostingInformation;
        $this->mailingInformation = $mailingInformation;
        $this->shopInformation = $shopInformation;
    }

    /**
     * @return array
     */
    public function getSummary()
    {
        return [
            'server' => $this->hostingInformation->getServerInformation(),
            'instaWebInstalled' => $this->hostingInformation->isApacheInstawebModule(),
            'uname' => $this->hostingInformation->getUname(),
            'hostname' => $this->hostingInformation->getHostname(),
            'database' => $this->hostingInformation->getDatabaseInformation(),
            'overrides' => $this->shopInformation->getOverridesList(),
            'shop' => $this->shopInformation->getShopInformation(),
            'isNativePHPmail' => $this->mailingInformation->isNativeMailUsed(),
            'smtp' => $this->mailingInformation->getSmtpInformation(),
        ];
    }
}
