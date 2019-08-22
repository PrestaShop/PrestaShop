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
use Language;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\CurrencyCommandInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\InvalidUnofficialCurrencyException;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;

/**
 * Class AbstractCurrencyHandler is responsible for encapsulating common behavior for legacy currency object model.
 *
 * @internal
 */
abstract class AbstractCurrencyHandler extends AbstractObjectModelHandler
{
    /**
     * @var LocaleRepository
     */
    protected $localeRepoCLDR;

    /**
     * @param LocaleRepository $localeRepoCLDR
     */
    public function __construct(LocaleRepository $localeRepoCLDR)
    {
        $this->localeRepoCLDR = $localeRepoCLDR;
    }

    /**
     * Associations conversion rate to given shop ids.
     *
     * @param Currency $entity
     * @param array $shopIds
     */
    protected function associateConversionRateToShops(Currency $entity, array $shopIds)
    {
        $columnsToUpdate = [];
        foreach ($shopIds as $shopId) {
            $columnsToUpdate[$shopId] = [
                'conversion_rate' => $entity->conversion_rate,
            ];
        }

        $this->updateMultiStoreColumns($entity, $columnsToUpdate);
    }

    /**
     * @param CurrencyCommandInterface $command
     *
     * @throws InvalidUnofficialCurrencyException
     */
    protected function assertUnofficialCurrencyDoesNotMatchAnyIsoCode(CurrencyCommandInterface $command)
    {
        if (!$command->isUnofficial() || null === $command->getIsoCode()) {
            return;
        }

        $isoCode = $command->getIsoCode()->getValue();
        $allLanguages = Language::getLanguages(false);
        foreach ($allLanguages as $languageData) {
            // CLDR locale give us the CLDR reference specification
            $cldrLocale = $this->localeRepoCLDR->getLocale($languageData['locale']);
            $cldrCurrency = $cldrLocale->getCurrency($isoCode);
            if (null !== $cldrCurrency) {
                throw new InvalidUnofficialCurrencyException(
                    sprintf(
                        'Unofficial currency with iso code "%s" is invalid because it matches a real currency',
                        $isoCode
                    ),
                    InvalidUnofficialCurrencyException::INVALID_ISO_CODE
                );
            }
        }
    }

    /**
     * @param CurrencyCommandInterface $command
     *
     * @throws InvalidUnofficialCurrencyException
     */
    protected function assertUnofficialCurrencyDoesNotMatchAnyNumericIsoCode(CurrencyCommandInterface $command)
    {
        if (!$command->isUnofficial() || null === $command->getNumericIsoCode()) {
            return;
        }

        $numericIsoCode = $command->getNumericIsoCode()->getValue();
        $allLanguages = Language::getLanguages(false);
        foreach ($allLanguages as $languageData) {
            // CLDR locale give us the CLDR reference specification
            $cldrLocale = $this->localeRepoCLDR->getLocale($languageData['locale']);
            foreach ($cldrLocale->getAllCurrencies() as $cldrCurrency) {
                if ($numericIsoCode == $cldrCurrency->getNumericIsoCode()) {
                    throw new InvalidUnofficialCurrencyException(
                        sprintf(
                            'Unofficial currency with numeric iso code "%s" is invalid because it matches a real currency',
                            $numericIsoCode
                        ),
                        InvalidUnofficialCurrencyException::INVALID_NUMERIC_ISO_CODE
                    );
                }
            }
        }
    }
}
