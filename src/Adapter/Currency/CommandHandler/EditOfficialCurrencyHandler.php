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

use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\EditCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDisableDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\DefaultCurrencyInMultiShopException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotUpdateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use Currency;
use PrestaShopException;
use PrestaShopDatabaseException;

/**
 * Class EditOfficialCurrencyHandler is responsible for updating currencies.
 *
 * @internal
 */
final class EditOfficialCurrencyHandler extends AbstractCurrencyHandler implements EditCurrencyHandlerInterface
{
    /**
     * @var CurrencyCommandValidator
     */
    private $validator;

    /**
     * @param LocaleRepository $localeRepository
     * @param LanguageInterface[] $languages
     * @param CurrencyCommandValidator $validator
     */
    public function __construct(
        LocaleRepository $localeRepository,
        array $languages,
        CurrencyCommandValidator $validator
    ) {
        parent::__construct($localeRepository, $languages);
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CurrencyException
     */
    public function handle(EditCurrencyCommand $command)
    {
        try {
            $entity = new Currency($command->getCurrencyId()->getValue());
            if (0 >= $entity->id) {
                throw new CurrencyNotFoundException(
                    sprintf(
                        'Currency object with id "%s" was not found for currency update',
                        $command->getCurrencyId()->getValue()
                    )
                );
            }
            $this->verify($entity, $command);
            $this->updateEntity($entity, $command);
        } catch (PrestaShopException $exception) {
            throw new CurrencyException(
                sprintf(
                    'An error occurred when updating currency object with id "%s"',
                    $command->getCurrencyId()->getValue()
                ),
                0,
                $exception
            );
        }
    }

    /**
     * @param Currency $entity
     * @param EditCurrencyCommand $command
     *
     * @throws CannotUpdateCurrencyException
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     * @throws LocalizationException
     */
    private function updateEntity(Currency $entity, EditCurrencyCommand $command)
    {
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

        $this->refreshLocalizedData($entity);

        if (false === $entity->update()) {
            throw new CannotUpdateCurrencyException(
                sprintf(
                    'An error occurred when updating currency object with id "%s"',
                    $command->getCurrencyId()->getValue()
                )
            );
        }

        if (!empty($command->getShopIds())) {
            $this->associateWithShops($entity, $command->getShopIds());
            $this->associateConversionRateToShops($entity, $command->getShopIds());
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
