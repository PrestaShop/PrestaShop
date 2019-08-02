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

namespace PrestaShop\PrestaShop\Core\Domain\Product\CustomizationField\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\CustomizationField\Exception\ProductCustomizationFieldConstraintException;
use function in_array;

/**
 * Holds products customization field.
 */
class CustomizationField
{
    public const TYPE_TEXT = 1;
    public const TYPE_FILE = 2;

    private $type;

    /**
     * @var string[]
     */
    private $localizedLabels;

    /**
     * @var bool
     */
    private $isRequired;

    /**
     * @param int $type
     * @param array $localizedLabels
     * @param bool $isRequired
     *
     * @throws ProductCustomizationFieldConstraintException
     */
    public function __construct(int $type, array $localizedLabels, bool $isRequired)
    {
        $this->assertIsValidType($type);

        $this->type = $type;
        $this->localizedLabels = $localizedLabels;
        $this->isRequired = $isRequired;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string[]
     */
    public function getLocalizedLabels(): array
    {
        return $this->localizedLabels;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    /**
     * @param int $type
     *
     * @throws ProductCustomizationFieldConstraintException
     */
    private function assertIsValidType(int $type): void
    {
        $availableTypes = [
            self::TYPE_TEXT,
            self::TYPE_FILE,
        ];

        if (!in_array($type, $availableTypes, true)) {
            throw new ProductCustomizationFieldConstraintException(
                sprintf(
                    'Invalid type %d given. Available types are text with id %d and file with id %d',
                    $type,
                    self::TYPE_TEXT,
                    self::TYPE_FILE
                ),
                ProductCustomizationFieldConstraintException::INVALID_TYPE
            );
        }
    }
}
