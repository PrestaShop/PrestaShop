<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Tax\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Tax\AbstractTaxHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\EditTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\EditTaxHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShopException;
use Tax;

/**
 * Handles command which is responsible for tax editing
 */
#[AsCommandHandler]
final class EditTaxHandler extends AbstractTaxHandler implements EditTaxHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws TaxException
     */
    public function handle(EditTaxCommand $command)
    {
        $tax = $this->getTax($command->getTaxId());

        if (null !== $command->getLocalizedNames()) {
            $tax->name = $command->getLocalizedNames();
        }
        if (null !== $command->getRate()) {
            $tax->rate = $command->getRate();
        }
        if (null !== $command->isEnabled()) {
            $tax->active = $command->isEnabled();
        }

        try {
            if (false === $tax->validateFields(false) || false === $tax->validateFieldsLang(false)) {
                throw new TaxException('Tax contains invalid field values');
            }

            if (!$tax->update()) {
                throw new TaxException(sprintf('Cannot update tax with id "%s"', $tax->id));
            }
        } catch (PrestaShopException) {
            throw new TaxException(sprintf('Cannot update tax with id "%s"', $tax->id));
        }
    }
}
