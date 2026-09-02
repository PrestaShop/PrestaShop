<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\BulkToggleCurrenciesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\BulkToggleCurrenciesStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\BulkToggleCurrenciesException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShopException;

/**
 * Toggles multiple currencies status using legacy Currency object model
 *
 * @internal
 */
#[AsCommandHandler]
final class BulkToggleCurrenciesStatusHandler extends AbstractCurrencyHandler implements BulkToggleCurrenciesStatusHandlerInterface
{
    /**
     * @var int
     */
    private $defaultCurrencyId;

    /**
     * @param int $defaultCurrencyId
     */
    public function __construct($defaultCurrencyId)
    {
        $this->defaultCurrencyId = (int) $defaultCurrencyId;
    }

    /**
     * @param BulkToggleCurrenciesStatusCommand $command
     *
     * @throws BulkToggleCurrenciesException
     */
    public function handle(BulkToggleCurrenciesStatusCommand $command)
    {
        $faileds = [];

        foreach ($command->getCurrencyIds() as $currency) {
            $entity = new Currency((int) $currency->getValue());

            if ($command->getStatus() == $entity->active) {
                continue;
            }

            if (0 >= $entity->id) {
                $faileds[] = $currency->getValue();
                continue;
            }

            if ($entity->active) {
                try {
                    $this->assertDefaultCurrencyIsNotBeingRemovedOrDisabled($currency->getValue(), $this->defaultCurrencyId);
                    $this->assertDefaultCurrencyIsNotBeingRemovedOrDisabledFromAnyShop($entity);
                } catch (CurrencyException) {
                    $faileds[] = $currency->getValue();
                    continue;
                }
            }

            try {
                if (false === $entity->toggleStatus()) {
                    $faileds[] = $currency->getValue();
                }
            } catch (PrestaShopException) {
                $faileds[] = $currency->getValue();
            }
        }

        if (!empty($faileds)) {
            throw new BulkToggleCurrenciesException($faileds, 'Failed to delete all of selected currencies');
        }
    }
}
