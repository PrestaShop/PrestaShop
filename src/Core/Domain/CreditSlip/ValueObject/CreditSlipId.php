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

namespace PrestaShop\PrestaShop\Core\Domain\CreditSlip\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CreditSlip\Exception\CreditSlipConstraintException;

/**
 * Provides identification data for Credit slip
 */
final class CreditSlipId
{
    /**
     * @var int
     */
    private $creditSlipId;

    /**
     * @param int $creditSlipId
     *
     * @throws CreditSlipConstraintException
     */
    public function __construct($creditSlipId)
    {
        $this->assertIsIntegerGreaterThanZero($creditSlipId);
        $this->creditSlipId = $creditSlipId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->creditSlipId;
    }

    /**
     * Validates that the value is integer and is greater than zero
     *
     * @param $value
     *
     * @throws CreditSlipConstraintException
     */
    private function assertIsIntegerGreaterThanZero($value)
    {
        if (!is_int($value) || 0 >= $value) {
            throw new CreditSlipConstraintException(sprintf('Invalid credit slip id "%s".', var_export($value, true)));
        }
    }
}
