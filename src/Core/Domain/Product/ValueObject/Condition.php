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

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Holds product condition either its new , used etc...
 */
class Condition
{
    public const NEW_CONDITION = 'new';

    public const USED_CONDITION = 'used';

    public const REFURBISHED_CONDITION = 'refurbished';

    public const AVAILABLE_CONDITIONS = [
        self::NEW_CONDITION,
        self::USED_CONDITION,
        self::REFURBISHED_CONDITION,
    ];

    /**
     * @var string
     */
    private $condition;

    /**
     * @param string $condition
     * @param bool $displayedOnProductPage
     *
     * @throws ProductConstraintException
     */
    public function __construct(string $condition)
    {
        if (!in_array($condition, self::AVAILABLE_CONDITIONS, true)) {
            throw new ProductConstraintException(
                sprintf(
                    'Non valid condition "%s" detected. Available values are "%s"',
                    $condition,
                    implode(',', self::AVAILABLE_CONDITIONS)
                ),
                ProductConstraintException::INVALID_CONDITION_TYPE
            );
        }

        $this->condition = $condition;
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }
}
