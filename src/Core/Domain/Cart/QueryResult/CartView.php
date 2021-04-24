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
     * @var int
     */
    private $currencyId;

    /**
     * @param int $cartId
     * @param int $currencyId
     * @param array $customerInformation
     * @param array $orderInformation
     * @param array $cartSummary
     */
    public function __construct(
        $cartId,
        $currencyId,
        array $customerInformation,
        array $orderInformation,
        array $cartSummary
    ) {
        $this->customerInformation = $customerInformation;
        $this->orderInformation = $orderInformation;
        $this->cartId = $cartId;
        $this->cartSummary = $cartSummary;
        $this->currencyId = $currencyId;
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
     * @return int
     */
    public function getCartCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * @return array
     */
    public function getCartSummary()
    {
        return $this->cartSummary;
    }
}
