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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilderConfig;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataField;

/**
 * Builds @see UpdateProductCommand for both single and All shops
 */
class UpdateProductCommandsBuilder implements MultiShopProductCommandsBuilderInterface
{
    /**
     * @var string
     */
    private $modifyAllNamePrefix;

    /**
     * @param string $modifyAllNamePrefix
     */
    public function __construct(string $modifyAllNamePrefix)
    {
        $this->modifyAllNamePrefix = $modifyAllNamePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $config = new CommandBuilderConfig($this->modifyAllNamePrefix);
        $this
            ->configureBasicInformation($config)
            ->configureOptions($config)
        ;

        $commandBuilder = new CommandBuilder($config);
        $shopCommand = new UpdateProductCommand($productId->getValue(), $singleShopConstraint);
        $allShopsCommand = new UpdateProductCommand($productId->getValue(), ShopConstraint::allShops());

        return $commandBuilder->buildCommands($formData, $shopCommand, $allShopsCommand);
    }

    /**
     * @param CommandBuilderConfig $config
     *
     * @return $this
     */
    private function configureBasicInformation(CommandBuilderConfig $config): self
    {
        $config
            ->addMultiShopField('[header][name]', 'setLocalizedNames', DataField::TYPE_ARRAY)
            ->addMultiShopField('[description][description]', 'setLocalizedDescriptions', DataField::TYPE_ARRAY)
            ->addMultiShopField('[description][description_short]', 'setLocalizedShortDescriptions', DataField::TYPE_ARRAY)
        ;

        return $this;
    }

    /**
     * @param CommandBuilderConfig $config
     *
     * @return $this
     */
    private function configureOptions(CommandBuilderConfig $config): self
    {
        $config
            ->addField('[description][manufacturer]', 'setManufacturerId', DataField::TYPE_INT)
            ->addMultiShopField('[options][visibility][online_only]', 'setOnlineOnly', DataField::TYPE_BOOL)
            ->addMultiShopField('[options][visibility][visibility]', 'setVisibility', DataField::TYPE_STRING)
            ->addMultiShopField('[options][visibility][available_for_order]', 'setAvailableForOrder', DataField::TYPE_BOOL)
            ->addMultiShopField('[options][visibility][show_price]', 'setShowPrice', DataField::TYPE_BOOL)
            ->addMultiShopField('[specifications][show_condition]', 'setShowCondition', DataField::TYPE_BOOL)
            ->addMultiShopField('[specifications][condition]', 'setCondition', DataField::TYPE_STRING)
        ;

        return $this;
    }
}
