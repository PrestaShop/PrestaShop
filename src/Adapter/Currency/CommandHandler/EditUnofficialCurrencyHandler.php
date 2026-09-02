<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditUnofficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\EditUnofficialCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDisableDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotUpdateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\DefaultCurrencyInMultiShopException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\InvalidUnofficialCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShopException;

/**
 * Class EditUnofficialCurrencyHandler is responsible for updating unofficial currencies.
 *
 * @internal
 */
#[AsCommandHandler]
final class EditUnofficialCurrencyHandler extends AbstractCurrencyHandler implements EditUnofficialCurrencyHandlerInterface
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
    public function handle(EditUnofficialCurrencyCommand $command)
    {
        try {
            $entity = new Currency($command->getCurrencyId()->getValue());
            if (0 >= $entity->id) {
                throw new CurrencyNotFoundException(sprintf('Currency object with id "%s" was not found for currency update', $command->getCurrencyId()->getValue()));
            }
            $this->verify($entity, $command);

            if (null !== $command->getIsoCode()) {
                $entity->iso_code = $command->getIsoCode()->getValue();
            }
            $this->updateEntity($entity, $command);
        } catch (PrestaShopException $exception) {
            throw new CurrencyException(sprintf('An error occurred when updating currency object with id "%s"', $command->getCurrencyId()->getValue()), 0, $exception);
        }
    }

    /**
     * @param Currency $entity
     * @param EditUnofficialCurrencyCommand $command
     *
     * @throws CannotDisableDefaultCurrencyException
     * @throws CurrencyConstraintException
     * @throws DefaultCurrencyInMultiShopException
     * @throws InvalidUnofficialCurrencyException
     */
    private function verify(Currency $entity, EditUnofficialCurrencyCommand $command)
    {
        $this->validator->assertDefaultCurrencyIsNotBeingDisabled($command);
        if (null !== $command->getIsoCode()) {
            $this->validator->assertCurrencyIsNotInReference($command->getIsoCode()->getValue());
            if ($entity->iso_code !== $command->getIsoCode()->getValue()) {
                $this->validator->assertCurrencyIsNotAvailableInDatabase($command->getIsoCode()->getValue());
            }
        }
        $this->validator->assertDefaultCurrencyIsNotBeingRemovedOrDisabledFromShop($entity, $command);
    }
}
