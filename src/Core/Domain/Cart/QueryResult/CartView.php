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

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult;

class CartView
{
    /**
     * @var array
     */
    private $customerInformation;

    /**
     * @var array
     */
    private $orderInformation;

    /**
     * @var int
     */
    private $cartId;

    /**
     * @var array
     */
    private $cartSummary;

    /**
     * @param int $cartId
     * @param array $customerInformation
     * @param array $orderInformation
     * @param array $cartSummary
     */
    public function __construct($cartId, array $customerInformation, array $orderInformation, array $cartSummary)
    {
        $this->customerInformation = $customerInformation;
        $this->orderInformation = $orderInformation;
        $this->cartId = $cartId;
        $this->cartSummary = $cartSummary;
    }

    /**
     * @return array
     */
    public function getCustomerInformation()
    {
        return $this->customerInformation;
    }

    /**
     * @return array
     */
    public function getOrderInformation()
    {
        return $this->orderInformation;
    }

    /**
     * @return int
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return array
     */
    public function getCartSummary()
    {
        return $this->cartSummary;
    }
}
