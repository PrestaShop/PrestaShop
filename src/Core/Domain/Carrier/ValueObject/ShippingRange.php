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

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject;

final class ShippingRange
{
    /**
     * @var float
     */
    private $valueFrom;

    /**
     * @var float
     */
    private $valueTo;

    /**
     * @var array
     */
    private $priceByZone;

    /**
     * @param float $valueFrom
     * @param float $valueTo
     * @param array $priceByZone
     */
    public function __construct($valueFrom, $valueTo, $priceByZone)
    {
        $this->valueFrom = $valueFrom;
        $this->valueTo = $valueTo;
        $this->priceByZone = $priceByZone;
    }

    /**
     * @return float
     */
    public function getValueFrom()
    {
        return $this->valueFrom;
    }

    /**
     * @return float
     */
    public function getValueTo()
    {
        return $this->valueTo;
    }

    /**
     * @return array
     */
    public function getPriceByZone()
    {
        return $this->priceByZone;
    }
}
