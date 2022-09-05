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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationCommandsBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataFormatter\BulkCombinationFormDataFormatter;

class BulkCombinationFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var CombinationCommandsBuilderInterface
     */
    private $commandsBuilder;

    /**
     * @var BulkCombinationFormDataFormatter
     */
    private $bulkCombinationFormDataFormatter;

    /**
     * @param CommandBusInterface $commandBus
     * @param BulkCombinationFormDataFormatter $bulkCombinationFormDataFormatter
     * @param CombinationCommandsBuilderInterface $commandsBuilder
     */
    public function __construct(
        CommandBusInterface $commandBus,
        BulkCombinationFormDataFormatter $bulkCombinationFormDataFormatter,
        CombinationCommandsBuilderInterface $commandsBuilder
    ) {
        $this->commandBus = $commandBus;
        $this->commandsBuilder = $commandsBuilder;
        $this->bulkCombinationFormDataFormatter = $bulkCombinationFormDataFormatter;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data)
    {
        // not used for creation
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $data): void
    {
        // @todo: a hook system should be integrated in this handler for extendability
        $formattedData = $this->bulkCombinationFormDataFormatter->format($data);
        $commands = $this->commandsBuilder->buildCommands(new CombinationId($id), $formattedData);

        foreach ($commands as $command) {
            $this->commandBus->handle($command);
        }
    }
}
