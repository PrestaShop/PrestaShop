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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Dto;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

class EditableCustomer
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var bool
     */
    private $isNewsletterSubscribed;

    /**
     * @var bool
     */
    private $isPartnerOfferSubscribed;

    /**
     * @param CustomerId $customerId
     * @param bool $isEnabled
     * @param bool $isNewsletterSubscribed
     * @param bool $isPartnerOfferSubscribed
     */
    public function __construct(
        CustomerId $customerId,
        $isEnabled,
        $isNewsletterSubscribed,
        $isPartnerOfferSubscribed
    ) {
        $this->customerId = $customerId;
        $this->isEnabled = $isEnabled;
        $this->isNewsletterSubscribed = $isNewsletterSubscribed;
        $this->isPartnerOfferSubscribed = $isPartnerOfferSubscribed;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @return bool
     */
    public function isNewsletterSubscribed()
    {
        return $this->isNewsletterSubscribed;
    }

    /**
     * @return bool
     */
    public function isPartnerOfferSubscribed()
    {
        return $this->isPartnerOfferSubscribed;
    }
}
