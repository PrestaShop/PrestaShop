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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use CustomizationField;
use PrestaShop\PrestaShop\Adapter\Product\ProductCustomizationFieldUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Repository\CustomizationFieldRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\UpdateCustomizationFieldCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\UpdateCustomizationFieldHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Handles @see UpdateCustomizationFieldCommand using legacy object model
 */
final class UpdateCustomizationFieldHandler implements UpdateCustomizationFieldHandlerInterface
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
     * @var CustomizationFieldRepository
     */
    private $customizationFieldRepository;

    /**
     * @param ProductRepository $productRepository
     * @param ProductCustomizationFieldUpdater $productCustomizationFieldUpdater
     * @param CustomizationFieldRepository $customizationFieldRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductCustomizationFieldUpdater $productCustomizationFieldUpdater,
        CustomizationFieldRepository $customizationFieldRepository
    ) {
        $this->productRepository = $productRepository;
        $this->productCustomizationFieldUpdater = $productCustomizationFieldUpdater;
        $this->customizationFieldRepository = $customizationFieldRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCustomizationFieldCommand $command): void
    {
        $customizationField = $this->customizationFieldRepository->get($command->getCustomizationFieldId());
        $this->fillEntityWithCommandData($customizationField, $command);

        $this->customizationFieldRepository->update($customizationField);

        $product = $this->productRepository->get(new ProductId((int) $customizationField->id_product));
        $this->productCustomizationFieldUpdater->refreshProductCustomizability($product);
    }

    /**
     * @param CustomizationField $customizationField
     * @param UpdateCustomizationFieldCommand $command
     *
     * @return CustomizationField
     */
    private function fillEntityWithCommandData(Customizationfield $customizationField, UpdateCustomizationFieldCommand $command): CustomizationField
    {
        if (null !== $command->getType()) {
            $customizationField->type = $command->getType()->getValue();
        }

        if (null !== $command->isAddedByModule()) {
            $customizationField->is_module = $command->isAddedByModule();
        }

        if (null !== $command->isRequired()) {
            $customizationField->required = $command->isRequired();
        }

        if (null !== $command->getLocalizedNames()) {
            $customizationField->name = $command->getLocalizedNames();
        }

        return $customizationField;
    }
}
