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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 *  Holds product combination identification data
 */
class CombinationId implements CombinationIdInterface
{
    /**
     * Indicates that no combination is provided/selected
     *
     * @deprecated since 1.7.9.0 and will be removed in next major version.
     * @see NoCombinationId instead
     */
    public const NO_COMBINATION = 0;

    /**
     * @var int
     */
    private $combinationId;

    /**
     * @param int $combinationId
     */
    public function __construct(int $combinationId)
    {
        $this->assertValueIsPositive($combinationId);
        $this->combinationId = $combinationId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->combinationId;
    }

    /**
     * @param int $value
     *
     * @throws ProductConstraintException
     */
    private function assertValueIsPositive(int $value)
    {
        if (0 >= $value) {
            throw new CombinationConstraintException(sprintf('Combination id must be positive integer. "%s" given', $value), CombinationConstraintException::INVALID_ID);
        }
    }
}
