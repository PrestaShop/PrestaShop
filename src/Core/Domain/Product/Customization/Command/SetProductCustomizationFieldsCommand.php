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

use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use RuntimeException;

/**
 * Sets product customization fields
 */
class SetProductCustomizationFieldsCommand
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
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @param int $productId
     * @param array $customizationFields
     * @param ShopConstraint $shopConstraint
     */
    public function __construct(
        int $productId,
        array $customizationFields,
        ShopConstraint $shopConstraint
    ) {
        $this->productId = new ProductId($productId);
        $this->shopConstraint = $shopConstraint;
        $this->setCustomizationFields($customizationFields);
        $this->shopConstraint = $shopConstraint;
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
     * @param array $customizationFields
     */
    public function setCustomizationFields(array $customizationFields): void
    {
        if (empty($customizationFields)) {
            throw new RuntimeException(sprintf(
                'Empty customization fields array provided in %s. To remove customization fields use %s',
                self::class,
                RemoveAllCustomizationFieldsFromProductCommand::class
            ));
        }
        foreach ($customizationFields as $customizationField) {
            $customizationField = $this->normalizeCustomizationFieldData($customizationField);
            $this->customizationFields[] =
                new CustomizationField(
                    (int) $customizationField['type'],
                    $customizationField['localized_names'],
                    (bool) $customizationField['is_required'],
                    $customizationField['modify_all_shops_name'],
                    (bool) isset($customizationField['added_by_module']) ? $customizationField['added_by_module'] : false,
                    isset($customizationField['id']) ? (int) $customizationField['id'] : null
                );
        }
    }

    private function normalizeCustomizationFieldData(array $originalCustomizationFieldData): array
    {
        $originalCustomizationFieldData['localized_names'] = $originalCustomizationFieldData['localized_names'] ?? $originalCustomizationFieldData['name'];
        $originalCustomizationFieldData['is_required'] = $originalCustomizationFieldData['is_required'] ?? $originalCustomizationFieldData['required'];

        return $originalCustomizationFieldData;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
