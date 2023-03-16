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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilderConfig;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataField;

/**
 * Builds commands from command stock form type only fields related to StockAvailable
 */
class UpdateCombinationStockAvailableCommandsBuilder implements CombinationCommandsBuilderInterface
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
    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $config = new CommandBuilderConfig($this->modifyAllNamePrefix);
        $config
            ->addMultiShopField('[stock][quantities][delta_quantity][delta]', 'setDeltaQuantity', DataField::TYPE_INT)
            ->addMultiShopField('[stock][quantities][fixed_quantity]', 'setFixedQuantity', DataField::TYPE_INT)
            ->addMultiShopField('[stock][options][stock_location]', 'setLocation', DataField::TYPE_STRING)
        ;

        $commandBuilder = new CommandBuilder($config);
        $shopCommand = new UpdateCombinationStockAvailableCommand($combinationId->getValue(), $singleShopConstraint);
        $allShopsCommand = new UpdateCombinationStockAvailableCommand($combinationId->getValue(), ShopConstraint::allShops());

        return $commandBuilder->buildCommands($formData, $shopCommand, $allShopsCommand);
    }
}
