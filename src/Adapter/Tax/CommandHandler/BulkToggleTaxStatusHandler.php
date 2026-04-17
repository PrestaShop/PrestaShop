<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Tax\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Tax\AbstractTaxHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkToggleTaxStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\BulkToggleTaxStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\UpdateTaxException;
use PrestaShopException;

/**
 * Handles command which toggles taxes status on bulk action using legacy object model
 */
#[AsCommandHandler]
final class BulkToggleTaxStatusHandler extends AbstractTaxHandler implements BulkToggleTaxStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkToggleTaxStatusCommand $command)
    {
        foreach ($command->getTaxIds() as $taxId) {
            $tax = $this->getTax($taxId);
            $tax->active = $command->getExpectedStatus();

            try {
                if (!$tax->save()) {
                    throw new UpdateTaxException(sprintf('Unable to toggle Tax with id "%s"', $taxId->getValue()), UpdateTaxException::FAILED_BULK_UPDATE_STATUS);
                }
            } catch (PrestaShopException) {
                throw new TaxException(sprintf('An error occurred when updating Tax status with id "%s"', $taxId->getValue()));
            }
        }
    }
}
