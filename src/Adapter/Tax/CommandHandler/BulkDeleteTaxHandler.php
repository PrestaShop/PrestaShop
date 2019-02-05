<?php

namespace PrestaShop\PrestaShop\Adapter\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkDeleteTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\BulkDeleteTaxHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use Tax;

/**
 * Class BulkDeleteTaxHandler handles command which deletes Taxes in bulk action
 */
class BulkDeleteTaxHandler extends AbstractTaxHandler implements BulkDeleteTaxHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteTaxCommand $command)
    {
        foreach ($command->getTaxesIds() as $taxId) {
            $taxIdValue = $taxId->getValue();
            $this->assertTaxWasFound($taxId, $entity = new Tax($taxIdValue));

            try {
                if (!$entity->delete()) {
                    sprintf(
                        'Cannot delete Tax object with id "%s"',
                        $taxIdValue
                    );
                }
            } catch (\PrestaShopException $e) {
                throw new TaxException(
                    sprintf(
                        'An error occurred when deleting Tax object with id "%s"',
                        $taxIdValue
                    )
                );
            }
        }
    }
}
