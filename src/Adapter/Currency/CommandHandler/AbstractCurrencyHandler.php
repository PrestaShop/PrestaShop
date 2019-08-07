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
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\InvalidCustomCurrencyException;
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
     * @param string $isoCode
     *
     * @throws InvalidCustomCurrencyException
     */
    protected function assertCustomCurrencyDoesNotMatchAnyIsoCode($isoCode)
    {
        $allLanguages = Language::getLanguages(false);
        foreach ($allLanguages as $languageData) {
            // CLDR locale give us the CLDR reference specification
            $cldrLocale = $this->localeRepoCLDR->getLocale($languageData['locale']);
            $cldrCurrency = $cldrLocale->getCurrency($isoCode);
            if (null !== $cldrCurrency) {
                throw new InvalidCustomCurrencyException(
                    sprintf(
                        'Custom currency with iso code "%s" is invalid because it matches a real currency',
                        $isoCode
                    ),
                    InvalidCustomCurrencyException::INVALID_ISO_CODE
                );
            }
        }
    }

    /**
     * @param int $numericIsoCode
     *
     * @throws InvalidCustomCurrencyException
     */
    protected function assertCustomCurrencyDoesNotMatchAnyNumericIsoCode($numericIsoCode)
    {
        $allLanguages = Language::getLanguages(false);
        foreach ($allLanguages as $languageData) {
            // CLDR locale give us the CLDR reference specification
            $cldrLocale = $this->localeRepoCLDR->getLocale($languageData['locale']);
            foreach ($cldrLocale->getAllCurrencies() as $cldrCurrency) {
                if ($numericIsoCode == $cldrCurrency->getNumericIsoCode()) {
                    throw new InvalidCustomCurrencyException(
                        sprintf(
                            'Custom currency with numeric iso code "%s" is invalid because it matches a real currency',
                            $numericIsoCode
                        ),
                        InvalidCustomCurrencyException::INVALID_NUMERIC_ISO_CODE
                    );
                }
            }
        }
    }
}
