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
final class Condition
{
    public const IS_NEW = 'new';

    public const IS_USED = 'used';

    public const IS_REFURBISHED = 'refurbished';

    public const AVAILABLE_CONDITIONS = [
        self::IS_NEW,
        self::IS_USED,
        self::IS_REFURBISHED,
    ];

    /**
     * @var string
     */
    private $condition;

    /**
     * @var bool
     */
    private $displayedOnProductPage;

    /**
     * @param string $condition
     * @param bool $displayedOnProductPage
     *
     * @throws ProductConstraintException
     */
    public function __construct(string $condition, bool $displayedOnProductPage)
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
        $this->displayedOnProductPage = $displayedOnProductPage;
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @return bool
     */
    public function isDisplayedOnProductPage(): bool
    {
        return $this->displayedOnProductPage;
    }
}
