<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Tax\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Tax\AbstractTaxHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\AddTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\AddTaxHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use PrestaShopException;
use Tax;

/**
 * Handles command which is responsible for tax editing
 */
#[AsCommandHandler]
final class AddTaxHandler extends AbstractTaxHandler implements AddTaxHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws TaxException
     */
    public function handle(AddTaxCommand $command)
    {
        $tax = new Tax();

        $tax->name = $command->getLocalizedNames();
        $tax->rate = $command->getRate();
        $tax->active = $command->isEnabled();

        try {
            if (false === $tax->validateFields(false) || false === $tax->validateFieldsLang(false)) {
                throw new TaxException('Tax contains invalid field values');
            }

            if (!$tax->save()) {
                throw new TaxException(sprintf('Cannot create tax with id "%s"', $tax->id));
            }
        } catch (PrestaShopException) {
            throw new TaxException(sprintf('Cannot create tax with id "%s"', $tax->id));
        }

        return new TaxId((int) $tax->id);
    }
}
