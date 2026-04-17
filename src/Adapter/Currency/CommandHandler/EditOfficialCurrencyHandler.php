<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\EditCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDisableDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotUpdateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\DefaultCurrencyInMultiShopException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShopException;

/**
 * Class EditOfficialCurrencyHandler is responsible for updating currencies.
 *
 * @internal
 */
#[AsCommandHandler]
final class EditOfficialCurrencyHandler extends AbstractCurrencyHandler implements EditCurrencyHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotDisableDefaultCurrencyException
     * @throws CannotUpdateCurrencyException
     * @throws CurrencyException
     * @throws CurrencyNotFoundException
     * @throws DefaultCurrencyInMultiShopException
     * @throws LocalizationException
     * @throws LanguageNotFoundException
     */
    public function handle(EditCurrencyCommand $command)
    {
        try {
            $entity = new Currency($command->getCurrencyId()->getValue());
            if (0 >= $entity->id) {
                throw new CurrencyNotFoundException(sprintf('Currency object with id "%s" was not found for currency update', $command->getCurrencyId()->getValue()));
            }
            $this->verify($entity, $command);
            $this->updateEntity($entity, $command);
        } catch (PrestaShopException $exception) {
            throw new CurrencyException(sprintf('An error occurred when updating currency object with id "%s"', $command->getCurrencyId()->getValue()), 0, $exception);
        }
    }

    /**
     * @param Currency $entity
     * @param EditCurrencyCommand $command
     *
     * @throws CannotDisableDefaultCurrencyException
     * @throws DefaultCurrencyInMultiShopException
     */
    private function verify(Currency $entity, EditCurrencyCommand $command)
    {
        $this->validator->assertDefaultCurrencyIsNotBeingDisabled($command);
        $this->validator->assertDefaultCurrencyIsNotBeingRemovedOrDisabledFromShop($entity, $command);
    }
}
