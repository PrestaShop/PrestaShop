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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\Exception\CustomizationFieldConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Adds customization field to a product
 */
class AddCustomizationFieldCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var int
     */
    private $type;

    /**
     * @var bool
     */
    private $required;

    /**
     * @var bool
     */
    private $addedByModule;

    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @param int $productId
     * @param int $type
     * @param bool $required
     * @param bool $addedByModule
     * @param string[] $localizedNames
     */
    public function __construct(
        int $productId,
        int $type,
        bool $required,
        array $localizedNames,
        bool $addedByModule = false
    ) {
        $this->assertCustomizationType($type);
        $this->productId = new ProductId($productId);
        $this->type = $type;
        $this->required = $required;
        $this->addedByModule = $addedByModule;
        $this->localizedNames = $localizedNames;
    }

    /**
     * @param int $value
     *
     * @throws CustomizationFieldConstraintException
     */
    private function assertCustomizationType(int $value): void
    {
        if (!in_array($value, CustomizationSettings::AVAILABLE_TYPES)) {
            throw new CustomizationFieldConstraintException(
                sprintf(
                    'Invalid customization type value %d. Available types are: %s',
                    $value,
                    implode(',', CustomizationSettings::AVAILABLE_TYPES)
                ),
                CustomizationFieldConstraintException::INVALID_TYPE
            );
        }
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return bool
     */
    public function isAddedByModule(): bool
    {
        return $this->addedByModule;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }
}
