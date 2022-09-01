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

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationStockCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilderConfig;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataField;

/**
 * Builds commands from command stock form type
 */
final class CombinationStockCommandsBuilder implements CombinationCommandsBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommands(CombinationId $combinationId, array $formData): array
    {
        $config = new CommandBuilderConfig();
        $config
            ->addField('[stock][quantities][delta_quantity][delta]', 'setDeltaQuantity', DataField::TYPE_INT)
            ->addField('[stock][quantities][fixed_quantity]', 'setFixedQuantity', DataField::TYPE_INT)
            ->addField('[stock][quantities][minimal_quantity]', 'setMinimalQuantity', DataField::TYPE_INT)
            ->addField('[stock][options][stock_location]', 'setLocation', DataField::TYPE_STRING)
            ->addField('[stock][options][low_stock_threshold]', 'setLowStockThreshold', DataField::TYPE_INT)
            ->addField('[stock][options][disabling_switch_low_stock_threshold]', 'setLowStockAlert', DataField::TYPE_BOOL)
            ->addField('[stock][pack_stock_type]', 'setPackStockType', DataField::TYPE_INT)
            ->addField('[stock][available_date]', 'setAvailableDate', DataField::TYPE_DATETIME)
        ;

        $commandBuilder = new CommandBuilder($config);
        $command = new UpdateCombinationStockCommand($combinationId->getValue());

        return $commandBuilder->buildCommands($formData, $command);
    }
}
