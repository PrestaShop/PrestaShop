<?php

namespace PrestaShop\PrestaShop\Adapter\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\ToggleTaxStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\ToggleTaxStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\CannotToggleTaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxNotFoundException;
use PrestaShopException;
use Tax;

class ToggleTaxStatusHandler implements ToggleTaxStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ToggleTaxStatusCommand $command)
    {
        $taxId = $command->getTaxId()->getValue();
        $entity = new Tax($taxId);

        if ($taxId !== $entity->id) {
            throw new TaxNotFoundException(
                sprintf(
                    'Tax object with id "%s" has not been found for deletion.',
                    $taxId
                )
            );
        }

        try {
            if (false === $entity->toggleStatus()) {
                throw new CannotToggleTaxException(
                    sprintf(
                        'Unable to toggle Tax with id "%s"',
                        $command->getTaxId()->getValue()
                    )
                );
            }
        } catch (PrestaShopException $e) {
            throw new TaxException(
                sprintf(
                    'An error occurred when toggling status for Tax object with id "%s"',
                    $command->getTaxId()->getValue()
                ),
                0,
                $e
            );
        }
    }
}
