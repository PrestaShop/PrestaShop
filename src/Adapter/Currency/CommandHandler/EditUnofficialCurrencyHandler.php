<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditUnofficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\EditUnofficialCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDisableDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotUpdateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\DefaultCurrencyInMultiShopException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\InvalidUnofficialCurrencyException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShopDatabaseException;
use PrestaShopException;

/**
 * Class EditUnofficialCurrencyHandler is responsible for updating unofficial currencies.
 *
 * @internal
 */
final class EditUnofficialCurrencyHandler extends AbstractCurrencyHandler implements EditUnofficialCurrencyHandlerInterface
{
    /**
     * @var CurrencyCommandValidator
     */
    private $validator;

    /**
     * @param LocaleRepository $localeRepoCLDR
     * @param LanguageInterface[] $languages
     * @param CurrencyCommandValidator $validator
     */
    public function __construct(
        LocaleRepository $localeRepoCLDR,
        array $languages,
        CurrencyCommandValidator $validator
    ) {
        parent::__construct($localeRepoCLDR, $languages);
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CurrencyException
     */
    public function handle(EditUnofficialCurrencyCommand $command)
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
     * @param EditUnofficialCurrencyCommand $command
     *
     * @throws CannotUpdateCurrencyException
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     * @throws LocalizationException
     */
    private function updateEntity(Currency $entity, EditUnofficialCurrencyCommand $command)
    {
        if (null !== $command->getIsoCode()) {
            $entity->iso_code = $command->getIsoCode()->getValue();
        }
        if (null !== $command->getExchangeRate()) {
            $entity->conversion_rate = $command->getExchangeRate()->getValue();
        }
        if (null !== $command->getPrecision()) {
            $entity->precision = $command->getPrecision()->getValue();
        }
        $entity->active = $command->isEnabled();

        if (!empty($command->getLocalizedNames())) {
            $entity->setLocalizedNames($command->getLocalizedNames());
        }
        if (!empty($command->getLocalizedSymbols())) {
            $entity->setLocalizedSymbols($command->getLocalizedSymbols());
        }
        if (!empty($command->getLocalizedTransformations())) {
            $this->applyPatternTransformations($entity, $command->getLocalizedTransformations());
        }

        $this->refreshLocalizedData($entity);

        //IMPORTANT: specify that we want to save null values
        if (false === $entity->update(true)) {
            throw new CannotUpdateCurrencyException(sprintf('An error occurred when updating currency object with id "%s"', $command->getCurrencyId()->getValue()));
        }

        if (!empty($command->getShopIds())) {
            $this->associateWithShops($entity, $command->getShopIds());
            $this->associateConversionRateToShops($entity, $command->getShopIds());
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
