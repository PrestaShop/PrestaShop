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

namespace PrestaShop\PrestaShop\Adapter\Product\Customization\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Customization\Repository\CustomizationFieldRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Query\GetProductCustomizationFields;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryHandler\GetProductCustomizationFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryResult\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Defines contract to handle @var GetProductCustomizationFields query
 */
final class GetProductCustomizationFieldsHandler implements GetProductCustomizationFieldsHandlerInterface
{
    /**
     * @var CustomizationFieldRepository
     */
    private $customizationFieldRepository;

    /**
     * @param CustomizationFieldRepository $customizationFieldRepository
     */
    public function __construct(
        CustomizationFieldRepository $customizationFieldRepository
    ) {
        $this->customizationFieldRepository = $customizationFieldRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetProductCustomizationFields $query): array
    {
        $fieldIds = $this->customizationFieldRepository->getCustomizationFieldIds($query->getProductId());

        $customizationFields = [];
        foreach ($fieldIds as $fieldId) {
            $customizationFields[] = $this->buildCustomizationField($fieldId, $query->getShopConstraint()->getShopId());
        }

        return $customizationFields;
    }

    /**
     * @param CustomizationFieldId $fieldId
     * @param ShopId $shopId
     *
     * @return CustomizationField
     */
    private function buildCustomizationField(CustomizationFieldId $fieldId, ShopId $shopId): CustomizationField
    {
        $fieldEntity = $this->customizationFieldRepository->getForShop($fieldId, $shopId);

        return new CustomizationField(
            $fieldId->getValue(),
            (int) $fieldEntity->type,
            $fieldEntity->name,
            (bool) $fieldEntity->required,
            (bool) $fieldEntity->is_module
        );
    }
}
