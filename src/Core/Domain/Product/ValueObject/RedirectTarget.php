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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Represents product or category id to which customer should be redirected in case product is disabled
 */
class RedirectTarget
{
    public const NO_TARGET = 0;

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     *
     * @throws ProductConstraintException
     */
    public function __construct(int $value)
    {
        $this->assertTargetValueIsValid($value);
        $this->value = $value;
    }

    public function isNoTarget(): bool
    {
        return $this->value === static::NO_TARGET;
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
     *
     * @throws ProductConstraintException
     */
    private function assertTargetValueIsValid(int $value): void
    {
        if ($value === static::NO_TARGET) {
            return;
        }

        if ($value <= 0) {
            throw new ProductConstraintException(
                sprintf('Invalid redirect target "%d". It cannot be less than or equal to 0', $value),
                ProductConstraintException::INVALID_REDIRECT_TARGET
            );
        }
    }
}
