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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command;

use LogicException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Updates product customization fields
 */
class UpdateProductCustomizationFieldsCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var CustomizationField[]
     */
    private $customizationFields = [];

    /**
     * Builds command to replace existing customization fields with provided ones.
     *
     * @param int $productId
     * @param array[] $customizationFields
     *
     * @see UpdateProductCustomizationFieldsCommand::setCustomizationFields() for $customizationFields array structure.
     *
     * @return static
     */
    public static function replace(int $productId, array $customizationFields): self
    {
        if (empty($customizationFields)) {
            throw new LogicException(sprintf(
                'Providing empty array will remove all customization fields. Use %s::deleteAll()', self::class
            ));
        }

        return new self(
            $productId,
            $customizationFields
        );
    }

    /**
     * Builds command to delete all existing CustomizationFields for provided product
     *
     * @param int $productId
     *
     * @return UpdateProductCustomizationFieldsCommand
     */
    public static function deleteAll(int $productId): self
    {
        return new self($productId, []);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return CustomizationField[]
     */
    public function getCustomizationFields(): array
    {
        return $this->customizationFields;
    }

    /**
     * Use static factories to initiate this class
     *
     * @param int $productId
     * @param array $customizationFields
     */
    private function __construct(int $productId, array $customizationFields)
    {
        $this->productId = new ProductId($productId);
        $this->setCustomizationFields($customizationFields);
    }

    /**
     * @param array $customizationFields
     */
    private function setCustomizationFields(array $customizationFields): void
    {
        foreach ($customizationFields as $customizationField) {
            $this->customizationFields[] = new CustomizationField(
                (int) $customizationField['type'],
                $customizationField['localized_names'],
                (bool) $customizationField['is_required'],
                (bool) $customizationField['added_by_module'],
                (int) $customizationField['id'] ?? null
            );
        }
    }
}
