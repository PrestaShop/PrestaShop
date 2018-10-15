<?php
/**
 * 2007-2018 PrestaShop.
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

/**
 * Class CustomerCartInformation.
 */
class CustomerCartInformation
{
    /**
     * @var string
     */
    private $cartId;

    /**
     * @var string
     */
    private $cartCreationDate;

    /**
     * @var string
     */
    private $cartTotal;

    /**
     * @var string
     */
    private $carrierName;

    /**
     * @param string $cartId
     * @param string $cartCreationDate
     * @param string $cartTotal
     * @param string $carrierName
     */
    public function __construct(
        $cartId,
        $cartCreationDate,
        $cartTotal,
        $carrierName
    ) {
        $this->cartId = $cartId;
        $this->cartCreationDate = $cartCreationDate;
        $this->cartTotal = $cartTotal;
        $this->carrierName = $carrierName;
    }

    /**
     * @return string
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return string
     */
    public function getCartCreationDate()
    {
        return $this->cartCreationDate;
    }

    /**
     * @return string
     */
    public function getCartTotal()
    {
        return $this->cartTotal;
    }

    /**
     * @return string
     */
    public function getCarrierName()
    {
        return $this->carrierName;
    }
}
