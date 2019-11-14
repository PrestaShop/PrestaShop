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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;

class Precision
{
    const DEFAULT_PRECISION = 2;

    /**
     * @var int
     */
    private $precision;

    /**
     * @param int $precision
     *
     * @throws CurrencyConstraintException
     */
    public function __construct(int $precision)
    {
        $this->assertIsPositiveInteger($precision);
        $this->precision = $precision;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->precision;
    }

    /**
     * @param int $precision
     *
     * @throws CurrencyConstraintException
     */
    private function assertIsPositiveInteger(int $precision)
    {
        if ((int) $precision < 0) {
            throw new CurrencyConstraintException(
                sprintf(
                    'Given precision "%s" is not valid. It must be a positive integer',
                    var_export($precision, true)
                ),
                CurrencyConstraintException::INVALID_PRECISION
            );
        }
    }
}
