<?php

namespace PrestaShop\PrestaShop\Adapter\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkUpdateTaxStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\BulkUpdateTaxStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\CannotToggleTaxStatusException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use Tax;

/**
 * Class BulkUpdateTaxStatusHandler handles command which updates Taxes status in bulk action
 */
final class BulkUpdateTaxStatusHandler extends AbstractTaxHandler implements BulkUpdateTaxStatusHandlerInterface
{

    /**
     * {@inheritdoc}
     */
    public function handle(BulkUpdateTaxStatusCommand $command)
    {
        foreach ($command->getTaxesIds() as $taxId) {
            $tax = new Tax($taxId->getValue());
            $this->assertTaxWasFound($taxId, $tax);

            try {
                if (!$tax->save()) {
                    throw new CannotToggleTaxStatusException(
                        sprintf(
                            'Unable to toggle Tax with id "%s"',
                            $taxId->getValue()
                        )
                    );
                }
            } catch (\PrestaShopException $e) {
                throw new TaxException(
                    sprintf(
                        'An error occurred when updating Tax status with id "%s"',
                        $taxId->getValue()
                    )
                );
            }
        }
    }
}
