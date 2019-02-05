<?php

namespace PrestaShop\PrestaShop\Adapter\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkUpdateTaxStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\BulkUpdateTaxStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use Tax;

/**
 * Class BulkUpdateTaxStatusHandler handles command which updates Taxes status in bulk action
 */
final class BulkUpdateTaxStatusHandler extends AbstractTaxHandler implements BulkUpdateTaxStatusHandlerInterface
{

    /**
     * @param BulkUpdateTaxStatusCommand $command
     * @throws TaxException
     * @throws \PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxNotFoundException
     */
    public function handle(BulkUpdateTaxStatusCommand $command)
    {
        foreach ($command->getTaxesIds() as $taxId) {
            $tax = new Tax($taxId->getValue());
            $this->assertTaxWasFound($taxId, $tax);
            $tax->active = $command->getStatus()->isEnabled();

            if (false === $tax->save()) {
                throw new TaxException(
                    sprintf(
                        'An error occurred when enabling status for Tax object with id "%s"',
                        $taxId->getValue()
                    )
                );
            }
        }
    }
}
