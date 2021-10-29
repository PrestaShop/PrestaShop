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

namespace PrestaShop\PrestaShop\Core\Domain\SpecificPrice\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\SpecificPriceId as ProductSpecificPriceId;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception\SpecificPriceConstraintException;

@trigger_error(
    sprintf(
        '%s is deprecated since version 8.0.0 and will be removed in the next major version.',
        SpecificPriceId::class
    ),
    E_USER_DEPRECATED
);

/**
 * @deprecated since 8.0.0 and will be removed in the next major version.
 * @see ProductSpecificPriceId
 */
class SpecificPriceId
{
    /**
     * @var int
     */
    private $specificPriceId;

    /**
     * @param int $specificPriceId
     *
     * @throws SpecificPriceConstraintException
     */
    public function __construct(int $specificPriceId)
    {
        $this->assertIsGreaterThanZero($specificPriceId);
        $this->specificPriceId = $specificPriceId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->specificPriceId;
    }

    /**
     * Validates that the value is greater than zero
     *
     * @param int $value
     *
     * @throws SpecificPriceConstraintException
     */
    private function assertIsGreaterThanZero(int $value): void
    {
        if (!is_int($value) || 0 >= $value) {
            throw new SpecificPriceConstraintException(sprintf('Invalid specific price id "%s".', $value), SpecificPriceConstraintException::INVALID_ID);
        }
    }
}
