<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationConstraintException;

/**
 * Holds value of customization type
 */
class CustomizationType
{
    /**
     * Value representing customization file type
     */
    const TYPE_FILE = 0;

    /**
     * Value representing customization text type
     */
    const TYPE_TEXT = 1;

    /**
     * Available customization types
     */
    const AVAILABLE_TYPES = [self::TYPE_FILE, self::TYPE_TEXT];

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->assertAvailableType($value);
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
     * @param int $value
     *
     * @throws CustomizationConstraintException
     */
    private function assertAvailableType(int $value): void
    {
        if (!in_array($value,self::AVAILABLE_TYPES)) {
            throw new CustomizationConstraintException(
                sprintf(
                    'Invalid customization type "%d". Available types are: %d',
                    $value,
                    implode(',', self::AVAILABLE_TYPES)
                ),
                CustomizationConstraintException::INVALID_TYPE
            );
        }
    }
}
