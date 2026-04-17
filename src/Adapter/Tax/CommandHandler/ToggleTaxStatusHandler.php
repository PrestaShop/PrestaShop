<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Tax\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Tax\AbstractTaxHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\ToggleTaxStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\ToggleTaxStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\UpdateTaxException;
use PrestaShopException;

/**
 * Handles command which toggles Tax status using legacy object model
 */
#[AsCommandHandler]
final class ToggleTaxStatusHandler extends AbstractTaxHandler implements ToggleTaxStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ToggleTaxStatusCommand $command)
    {
        $tax = $this->getTax($command->getTaxId());
        $taxIdValue = $command->getTaxId()->getValue();
        $tax->active = $command->getExpectedStatus();

        try {
            if (!$tax->save()) {
                throw new UpdateTaxException(sprintf('Unable to toggle Tax with id "%s"', $taxIdValue), UpdateTaxException::FAILED_UPDATE_STATUS);
            }
        } catch (PrestaShopException) {
            throw new TaxException(sprintf('An error occurred when enabling ETax with id "%s"', $taxIdValue));
        }
    }
}
