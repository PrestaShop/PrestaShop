<?php

namespace PrestaShop\PrestaShop\Adapter\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\ToggleTaxStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\ToggleTaxStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\CannotToggleTaxStatusException;
use Tax;

final class ToggleTaxStatusHandler extends AbstractTaxHandler implements ToggleTaxStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ToggleTaxStatusCommand $command)
    {
        $taxIdValue = $command->getTaxId()->getValue();
        $this->assertTaxWasFound($command->getTaxId(), $entity = new Tax($taxIdValue));

        if (false === $entity->toggleStatus()) {
            throw new CannotToggleTaxStatusException(
                sprintf(
                    'Unable to toggle Tax with id "%s"',
                    $command->getTaxId()->getValue()
                )
            );
        }
    }
}
