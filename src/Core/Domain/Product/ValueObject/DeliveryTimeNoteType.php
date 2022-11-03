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
 * Holds value of additional delivery time notes type
 */
class DeliveryTimeNoteType
{
    /**
     * Represents case when additional delivery time note is not used
     */
    public const TYPE_NONE = 0;

    /**
     * Represents case when additional delivery time notes should be taken from default settings
     */
    public const TYPE_DEFAULT = 1;

    /**
     * Represents case when specific additional delivery time notes should be used
     */
    public const TYPE_SPECIFIC = 2;

    /**
     * A list of allowed type values
     */
    public const ALLOWED_TYPES = [
        'none' => self::TYPE_NONE,
        'default' => self::TYPE_DEFAULT,
        'specific' => self::TYPE_SPECIFIC,
    ];

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->assertTypeValueIsValid($value);
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $type
     *
     * @throws ProductConstraintException
     */
    private function assertTypeValueIsValid(int $type): void
    {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid type value of delivery time notes. Got "%d", allowed values are: %s',
                    $type,
                    implode(',', self::ALLOWED_TYPES)
                ),
                ProductConstraintException::INVALID_ADDITIONAL_TIME_NOTES_TYPE
            );
        }
    }
}
