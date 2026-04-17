<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Tax\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Tax\AbstractTaxHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkDeleteTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\BulkDeleteTaxHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\DeleteTaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShopException;

/**
 * Handles command which deletes Taxes on bulk action using legacy object model
 */
#[AsCommandHandler]
final class BulkDeleteTaxHandler extends AbstractTaxHandler implements BulkDeleteTaxHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteTaxCommand $command)
    {
        foreach ($command->getTaxIds() as $taxId) {
            $taxIdValue = $taxId->getValue();
            $tax = $this->getTax($taxId);

            try {
                if (!$tax->delete()) {
                    throw new DeleteTaxException(sprintf('Cannot delete Tax object with id "%s"', $taxIdValue), DeleteTaxException::FAILED_BULK_DELETE);
                }
            } catch (PrestaShopException) {
                throw new TaxException(sprintf('An error occurred when deleting Tax object with id "%s"', $taxIdValue));
            }
        }
    }
}
