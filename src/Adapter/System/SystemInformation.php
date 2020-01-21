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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
            'notHostMode' => !$this->hostingInformation->isHostMode(),
            'server' => $this->hostingInformation->getServerInformation(),
            'instaWebInstalled' => $this->hostingInformation->isApacheInstawebModule(),
            'uname' => $this->hostingInformation->getUname(),
            'database' => $this->hostingInformation->getDatabaseInformation(),
            'shop' => $this->shopInformation->getShopInformation(),
            'isNativePHPmail' => $this->mailingInformation->isNativeMailUsed(),
            'smtp' => $this->mailingInformation->getSmtpInformation(),
        ];
    }
}
