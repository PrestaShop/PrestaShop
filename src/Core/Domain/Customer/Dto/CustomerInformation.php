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

class CustomerInformation
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var PersonalInformation
     */
    private $personalInformation;

    /**
     * @var CustomerOrdersInformation
     */
    private $customerOrdersInformation;

    /**
     * @var CustomerCartsInformation
     */
    private $customerCartsInformation;

    /**
     * @var CustomerProductsInformation
     */
    private $customerProductsInformation;

    /**
     * @var CustomerMessageInformation[]
     */
    private $customerMessagesInformation;

    /**
     * @var DiscountInformation[]
     */
    private $discountsInformation;

    /**
     * @param CustomerId $customerId
     * @param PersonalInformation $generalInformation
     * @param CustomerOrdersInformation $customerOrdersInformation
     * @param CustomerCartsInformation $customerCartsInformation
     * @param CustomerProductsInformation $customerProductsInformation
     * @param CustomerMessageInformation[] $customerMessagesInformation
     * @param DiscountInformation[] $discountsInformation
     */
    public function __construct(
        CustomerId $customerId,
        PersonalInformation $generalInformation,
        CustomerOrdersInformation $customerOrdersInformation,
        CustomerCartsInformation $customerCartsInformation,
        CustomerProductsInformation $customerProductsInformation,
        array $customerMessagesInformation,
        array $discountsInformation
    ) {
        $this->customerId = $customerId;
        $this->personalInformation = $generalInformation;
        $this->customerOrdersInformation = $customerOrdersInformation;
        $this->customerCartsInformation = $customerCartsInformation;
        $this->customerProductsInformation = $customerProductsInformation;
        $this->customerMessagesInformation = $customerMessagesInformation;
        $this->discountsInformation = $discountsInformation;
    }
}
