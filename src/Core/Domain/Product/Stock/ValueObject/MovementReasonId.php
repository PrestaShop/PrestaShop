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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\MovementReasonConstraintException;

/**
 * Stock movement reason identifier.
 */
class MovementReasonId
{
    /**
     * Configuration keys for mapping MovementReason to id.
     *
     * @todo: add other keys
     */
    public const INCREASE_BY_EMPLOYEE_EDITION_CONFIG_KEY = 'PS_STOCK_MVT_INC_EMPLOYEE_EDITION';
    public const DECREASE_BY_EMPLOYEE_EDITION_CONFIG_KEY = 'PS_STOCK_MVT_DEC_EMPLOYEE_EDITION';

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $movementReasonId
     *
     * @throws MovementReasonConstraintException
     */
    public function __construct(int $movementReasonId)
    {
        $this->assertIsGreaterThanZero($movementReasonId);

        $this->value = $movementReasonId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    private function assertIsGreaterThanZero(int $value): void
    {
        if (0 >= $value) {
            throw new MovementReasonConstraintException(
                sprintf('Stock MovementReasonId %s is invalid.', $value),
                MovementReasonConstraintException::INVALID_ID
            );
        }
    }
}
