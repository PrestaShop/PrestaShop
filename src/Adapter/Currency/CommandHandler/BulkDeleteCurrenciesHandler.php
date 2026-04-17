<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\BulkDeleteCurrenciesCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\BulkDeleteCurrenciesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\BulkDeleteCurrenciesException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShopException;

/**
 * Deletes currencies using legacy currency object model
 *
 * @internal
 */
#[AsCommandHandler]
final class BulkDeleteCurrenciesHandler extends AbstractCurrencyHandler implements BulkDeleteCurrenciesHandlerInterface
{
    /**
     * @var int
     */
    private $defaultCurrencyId;

    /**
     * @param int $defaultCurrencyId
     */
    public function __construct(int $defaultCurrencyId)
    {
        $this->defaultCurrencyId = (int) $defaultCurrencyId;
    }

    /**
     * @param BulkDeleteCurrenciesCommand $command
     *
     * @throws BulkDeleteCurrenciesException
     */
    public function handle(BulkDeleteCurrenciesCommand $command)
    {
        $faileds = [];

        foreach ($command->getCurrencyIds() as $currencyId) {
            $entity = new Currency($currencyId->getValue());

            if (0 >= $entity->id) {
                $faileds[] = $currencyId->getValue();
                continue;
            }

            try {
                $this->assertDefaultCurrencyIsNotBeingRemovedOrDisabled($currencyId->getValue(), $this->defaultCurrencyId);
                $this->assertDefaultCurrencyIsNotBeingRemovedOrDisabledFromAnyShop($entity);
            } catch (CurrencyException) {
                $faileds[] = $currencyId->getValue();
                continue;
            }

            try {
                if (false === $entity->delete()) {
                    $faileds[] = $currencyId->getValue();
                }
            } catch (PrestaShopException) {
                $faileds[] = $currencyId->getValue();
            }
        }

        if (!empty($faileds)) {
            throw new BulkDeleteCurrenciesException($faileds, 'Failed to delete all of selected currencies');
        }
    }
}
