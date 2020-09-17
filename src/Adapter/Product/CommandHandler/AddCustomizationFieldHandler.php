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
use PrestaShop\PrestaShop\Adapter\Product\CustomizationFieldPersister;
use PrestaShop\PrestaShop\Adapter\Product\ProductProvider;
use PrestaShop\PrestaShop\Adapter\Product\ProductUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\AddCustomizationFieldCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\AddCustomizationFieldHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;

/**
 * Handles @see AddCustomizationFieldCommand using legacy object model
 */
final class AddCustomizationFieldHandler implements AddCustomizationFieldHandlerInterface
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
     * @var CustomizationFieldPersister
     */
    private $customizationFieldPersister;

    /**
     * @param ProductProvider $productProvider
     * @param ProductUpdater $productUpdater
     * @param CustomizationFieldPersister $customizationFieldPersister
     */
    public function __construct(
        ProductProvider $productProvider,
        ProductUpdater $productUpdater,
        CustomizationFieldPersister $customizationFieldPersister
    ) {
        $this->productProvider = $productProvider;
        $this->customizationFieldPersister = $customizationFieldPersister;
        $this->productUpdater = $productUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddCustomizationFieldCommand $command): CustomizationFieldId
    {
        $product = $this->productProvider->get($command->getProductId());
        $customizationField = new CustomizationField();

        $customizationField->id_product = (int) $product->id;
        $customizationField->type = $command->getType()->getValue();
        $customizationField->required = $command->isRequired();
        $customizationField->is_module = $command->isAddedByModule();
        $customizationField->name = $command->getLocalizedNames();

        $customizationFieldId = $this->customizationFieldPersister->add($customizationField);
        $this->productUpdater->refreshProductCustomizabilityProperties($product);

        return $customizationFieldId;
    }
}
