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
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Query\GetProductCustomizationFields;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryHandler\GetProductCustomizationFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryResult\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;

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
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param CustomizationFieldRepository $customizationFieldRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        CustomizationFieldRepository $customizationFieldRepository,
        ProductRepository $productRepository
    ) {
        $this->customizationFieldRepository = $customizationFieldRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetProductCustomizationFields $query): array
    {
        $productId = $query->getProductId();
        $product = $this->productRepository->get($productId);

        $fieldIds = $product->getNonDeletedCustomizationFieldIds();

        $customizationFields = [];
        foreach ($fieldIds as $fieldId) {
            $customizationFields[] = $this->buildCustomizationField((int) $fieldId);
        }

        return $customizationFields;
    }

    /**
     * @param int $fieldId
     *
     * @return CustomizationField
     */
    private function buildCustomizationField(int $fieldId): CustomizationField
    {
        $fieldEntity = $this->customizationFieldRepository->get(new CustomizationFieldId($fieldId));

        return new CustomizationField(
            $fieldId,
            (int) $fieldEntity->type,
            $fieldEntity->name,
            (bool) $fieldEntity->required,
            (bool) $fieldEntity->is_module
        );
    }
}
