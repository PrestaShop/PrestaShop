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

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Holds value which represents how customizable the product is
 */
class ProductCustomizability
{
    /**
     * The product does not have any customiztion fields, so it is not customizable at all
     */
    const NOT_CUSTOMIZABLE = 0;

    /**
     * The product has at least one customization field, but none of them are required
     */
    const ALLOWS_CUSTOMIZATION = 1;

    /**
     * The product has at least one customization field which is required
     */
    const REQUIRES_CUSTOMIZATION = 2;

    /**
     * Allowed customizability values
     */
    const ALLOWED_CUSTOMIZABILITY_VALUES = [
        self::NOT_CUSTOMIZABLE => self::NOT_CUSTOMIZABLE,
        self::ALLOWS_CUSTOMIZATION => self::ALLOWS_CUSTOMIZATION,
        self::REQUIRES_CUSTOMIZATION => self::REQUIRES_CUSTOMIZATION,
    ];

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
        $this->assertIsValidCustomizabilityValue($value);
        $this->value = $value;
    }

    /**
     * @param int $value
     *
     * @throws ProductConstraintException
     */
    private function assertIsValidCustomizabilityValue(int $value): void
    {
        if (in_array($value, self::ALLOWED_CUSTOMIZABILITY_VALUES)) {
            return;
        }

        throw new ProductConstraintException(
            sprintf(
                'Invalid product customizability value "%d". Allowed values are: %s',
                $value,
                implode(',', self::ALLOWED_CUSTOMIZABILITY_VALUES)
            ),
            ProductConstraintException::INVALID_CUSTOMIZABILITY
        );
    }
}
