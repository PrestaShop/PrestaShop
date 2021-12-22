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

namespace PrestaShop\PrestaShop\Adapter\Product\Customization\CommandHandler;

use CustomizationField;
use PrestaShop\PrestaShop\Adapter\Product\Customization\Update\ProductCustomizationFieldUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\SetProductCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\SetProductCustomizationFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationField as CustomizationFieldDTO;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Handles @see SetProductCustomizationFieldsCommand using legacy object model
 */
class SetProductCustomizationFieldsHandler implements SetProductCustomizationFieldsHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductCustomizationFieldUpdater
     */
    private $productCustomizationFieldUpdater;

    /**
     * @param ProductRepository $productRepository
     * @param ProductCustomizationFieldUpdater $productCustomizationFieldUpdater
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductCustomizationFieldUpdater $productCustomizationFieldUpdater
    ) {
        $this->productRepository = $productRepository;
        $this->productCustomizationFieldUpdater = $productCustomizationFieldUpdater;
    }

    /**
     * {@inheritdoc}
     *
     * Creates, updates or deletes customization fields depending on differences of existing and provided fields
     */
    public function handle(SetProductCustomizationFieldsCommand $command): array
    {
        $productId = $command->getProductId();
        $product = $this->productRepository->get($productId);
        $customizationFields = [];

        foreach ($command->getCustomizationFields() as $providedCustomizationField) {
            $customizationFields[] = $this->buildEntityFromDTO($productId, $providedCustomizationField);
        }

        $this->productCustomizationFieldUpdater->setProductCustomizationFields($productId, $customizationFields);

        return array_map(function (int $customizationFieldId): CustomizationFieldId {
            return new CustomizationFieldId($customizationFieldId);
        }, $product->getNonDeletedCustomizationFieldIds());
    }

    /**
     * @param ProductId $productId
     * @param CustomizationFieldDTO $customizationFieldDTO
     *
     * @return CustomizationField
     */
    private function buildEntityFromDTO(ProductId $productId, CustomizationFieldDTO $customizationFieldDTO): CustomizationField
    {
        $customizationField = new CustomizationField();
        $customizationField->id = $customizationFieldDTO->getCustomizationFieldId();
        $customizationField->id_product = $productId->getValue();
        $customizationField->type = $customizationFieldDTO->getType();
        $customizationField->required = $customizationFieldDTO->isRequired();
        $customizationField->name = $customizationFieldDTO->getLocalizedNames();
        $customizationField->is_module = $customizationFieldDTO->isAddedByModule();

        return $customizationField;
    }
}
