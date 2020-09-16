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

use PrestaShop\PrestaShop\Adapter\Product\AbstractCustomizationFieldHandler;
use PrestaShop\PrestaShop\Adapter\Product\CustomizationFieldUpdater;
use PrestaShop\PrestaShop\Adapter\Product\ProductProvider;
use PrestaShop\PrestaShop\Adapter\Product\ProductUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\UpdateCustomizationFieldCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\UpdateCustomizationFieldHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Handles @see UpdateCustomizationFieldCommand using legacy object model
 */
final class UpdateCustomizationFieldHandler extends AbstractCustomizationFieldHandler implements UpdateCustomizationFieldHandlerInterface
{
    /**
     * @var ProductProvider
     */
    private $productProvider;

    /**
     * @var ProductUpdater
     */
    private $productUpdater;

    /**
     * @var CustomizationFieldUpdater
     */
    private $customizationFieldUpdater;

    /**
     * @param ProductProvider $productProvider
     * @param ProductUpdater $productUpdater
     * @param CustomizationFieldUpdater $customizationFieldUpdater
     */
    public function __construct(
        ProductProvider $productProvider,
        ProductUpdater $productUpdater,
        CustomizationFieldUpdater $customizationFieldUpdater
    ) {
        $this->productProvider = $productProvider;
        $this->productUpdater = $productUpdater;
        $this->customizationFieldUpdater = $customizationFieldUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCustomizationFieldCommand $command): void
    {
        $customizationField = $this->getCustomizationField($command->getCustomizationFieldId());
        $this->customizationFieldUpdater->update($customizationField, $this->formatPropertiesForUpdate($command));

        $product = $this->productProvider->get(new ProductId((int) $customizationField->id_product));
        $this->productUpdater->refreshProductCustomizabilityProperties($product);
    }

    /**
     * @param UpdateCustomizationFieldCommand $command
     *
     * @return array
     */
    private function formatPropertiesForUpdate(UpdateCustomizationFieldCommand $command): array
    {
        $properties = [];

        if (null !== $command->getType()) {
            $properties['type'] = $command->getType()->getValue();
        }

        if (null !== $command->isAddedByModule()) {
            $properties['is_module'] = $command->isAddedByModule();
        }

        if (null !== $command->isRequired()) {
            $properties['required'] = $command->isRequired();
        }

        if (null !== $command->getLocalizedNames()) {
            $properties['name'] = $command->getLocalizedNames();
        }

        return $properties;
    }
}
