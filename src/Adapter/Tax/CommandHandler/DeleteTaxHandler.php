<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Tax\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Tax\AbstractTaxHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\DeleteTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\DeleteTaxHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\DeleteTaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShopException;

/**
 * Handles command which deletes Tax using legacy object model
 */
#[AsCommandHandler]
final class DeleteTaxHandler extends AbstractTaxHandler implements DeleteTaxHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteTaxCommand $command)
    {
        $tax = $this->getTax($command->getTaxId());
        $taxIdValue = $command->getTaxId()->getValue();

        try {
            if (!$tax->delete()) {
                throw new DeleteTaxException(sprintf('Cannot delete Tax object with id "%s"', $taxIdValue), DeleteTaxException::FAILED_DELETE);
            }
        } catch (PrestaShopException) {
            throw new TaxException(sprintf('An error occurred when deleting Tax object with id "%s"', $taxIdValue));
        }
    }
}
