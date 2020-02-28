<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddUnofficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\AddUnofficialCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotCreateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\InvalidUnofficialCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShopException;

/**
 * Adds a new unofficial currency.
 *
 * @internal
 */
final class AddUnofficialCurrencyHandler extends AbstractCurrencyHandler implements AddUnofficialCurrencyHandlerInterface
{
    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    /**
     * @param LocaleRepository $localeRepoCLDR
     * @param LanguageInterface[] $languages
     * @param CurrencyCommandValidator $validator
     * @param CurrencyDataProviderInterface $currencyDataProvider
     */
    public function __construct(
        LocaleRepository $localeRepoCLDR,
        array $languages,
        CurrencyCommandValidator $validator,
        CurrencyDataProviderInterface $currencyDataProvider
    ) {
        parent::__construct($localeRepoCLDR, $languages, $validator);
        $this->currencyDataProvider = $currencyDataProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CannotCreateCurrencyException
     * @throws CurrencyException
     * @throws CurrencyConstraintException
     * @throws InvalidUnofficialCurrencyException
     * @throws LanguageNotFoundException
     * @throws LocalizationException
     */
    public function handle(AddUnofficialCurrencyCommand $command)
    {
        $this->validator->assertCurrencyIsNotInReference($command->getIsoCode()->getValue());
        $this->validator->assertCurrencyIsNotAvailableInDatabase($command->getIsoCode()->getValue());

        try {
            $entity = $this->currencyDataProvider->getCurrencyByIsoCodeOrCreate($command->getIsoCode()->getValue());

            $entity->unofficial = true;
            $entity->numeric_iso_code = null;
            if (null !== $command->getPrecision()) {
                $entity->precision = $command->getPrecision()->getValue();
            }

            $this->addEntity($entity, $command);
        } catch (PrestaShopException $exception) {
            throw new CurrencyException('Failed to create new currency', 0, $exception);
        }

        return new CurrencyId((int) $entity->id);
    }
}
