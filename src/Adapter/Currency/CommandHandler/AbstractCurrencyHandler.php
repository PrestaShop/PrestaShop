<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Configuration;
use Currency;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotCreateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDeleteDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotUpdateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\DefaultCurrencyInMultiShopException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\Currency\PatternTransformer;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShopDatabaseException;
use PrestaShopException;
use Shop;

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
     * @var LanguageInterface[]
     */
    protected $languages;

    /**
     * @var CurrencyCommandValidator
     */
    protected $validator;

    /**
     * @var PatternTransformer
     */
    protected $patternTransformer;

    /**
     * @param LocaleRepository $localeRepoCLDR
     * @param LanguageInterface[] $languages
     * @param CurrencyCommandValidator $validator
     * @param PatternTransformer $patternTransformer
     */
    public function __construct(
        LocaleRepository $localeRepoCLDR,
        array $languages,
        CurrencyCommandValidator $validator,
        PatternTransformer $patternTransformer
    ) {
        $this->localeRepoCLDR = $localeRepoCLDR;
        $this->languages = $languages;
        $this->validator = $validator;
        $this->patternTransformer = $patternTransformer;
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
     * @param Currency $entity
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    protected function refreshLocalizedData(Currency $entity)
    {
        $languagesData = [];
        foreach ($this->languages as $language) {
            $languagesData[] = [
                'id_lang' => $language->getId(),
            ];
        }

        //This method will insert the missing localized names/symbols and detect if the currency has been modified
        $entity->refreshLocalizedCurrencyData($languagesData, $this->localeRepoCLDR);
    }

    /**
     * @param Currency $entity
     * @param array $localizedTransformations
     *
     * @throws LanguageNotFoundException
     */
    protected function applyPatternTransformations(Currency $entity, array $localizedTransformations)
    {
        $localizedPatterns = [];
        foreach ($localizedTransformations as $langId => $transformationType) {
            if (empty($transformationType)) {
                continue;
            }

            $languageCurrencyPattern = $this->getCurrencyPatternByLanguageId($langId);
            $localizedPatterns[$langId] = $this->patternTransformer->transform($languageCurrencyPattern, $transformationType);
        }
        $entity->setLocalizedPatterns($localizedPatterns);
    }

    /**
     * @param Currency $entity
     * @param AddCurrencyCommand $command
     *
     * @throws CannotCreateCurrencyException
     * @throws LanguageNotFoundException
     * @throws LocalizationException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    protected function addEntity(Currency $entity, AddCurrencyCommand $command)
    {
        $entity->iso_code = $command->getIsoCode()->getValue();
        $entity->active = $command->isEnabled();
        $entity->deleted = false;
        $entity->conversion_rate = $command->getExchangeRate()->getValue();

        // When creating a currency we reset the fields even if empty (then they will be generated by refreshLocalizedData)
        $entity->setLocalizedNames($command->getLocalizedNames());
        $entity->setLocalizedSymbols($command->getLocalizedSymbols());
        $this->applyPatternTransformations($entity, $command->getLocalizedTransformations());

        $this->refreshLocalizedData($entity);
        $this->validateCurrency($entity);

        //IMPORTANT: specify that we want to save null values
        if (false === $entity->save(true, true)) {
            throw new CannotCreateCurrencyException('Failed to create new currency');
        }

        $this->associateWithShops($entity, $command->getShopIds());
        $this->associateConversionRateToShops($entity, $command->getShopIds());
    }

    /**
     * @param Currency $entity
     * @param EditCurrencyCommand $command
     *
     * @throws CannotUpdateCurrencyException
     * @throws LanguageNotFoundException
     * @throws LocalizationException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    protected function updateEntity(Currency $entity, EditCurrencyCommand $command)
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
        if (!empty($command->getLocalizedTransformations())) {
            $this->applyPatternTransformations($entity, $command->getLocalizedTransformations());
        }

        $this->refreshLocalizedData($entity);
        $this->validateCurrency($entity);

        //IMPORTANT: specify that we want to save null values
        if (false === $entity->update(true)) {
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
     * @param Currency $currency
     *
     * @throws CurrencyConstraintException
     * @throws PrestaShopException
     */
    private function validateCurrency(Currency $currency): void
    {
        $this->validateLocalizedField($currency, 'name', CurrencyConstraintException::INVALID_NAME);
        $this->validateLocalizedField($currency, 'symbol', CurrencyConstraintException::INVALID_SYMBOL);
        $this->validateLocalizedField($currency, 'pattern', CurrencyConstraintException::INVALID_SYMBOL);
    }

    /**
     * @param Currency $currency
     * @param string $propertyName
     * @param int $errorCode
     *
     * @throws CurrencyConstraintException
     * @throws PrestaShopException
     */
    private function validateLocalizedField(Currency $currency, string $propertyName, int $errorCode): void
    {
        $localizedValues = $currency->{$propertyName};

        foreach ($localizedValues as $langId => $value) {
            if (true !== $currency->validateField($propertyName, $value, $langId)) {
                throw new CurrencyConstraintException(
                    sprintf(
                        'Invalid Currency localized property "%s" for language with id "%d"',
                        $propertyName,
                        $langId
                    ),
                    $errorCode
                );
            }
        }
    }

    /**
     * @param int $langId
     *
     * @return string
     *
     * @throws LanguageNotFoundException
     */
    private function getCurrencyPatternByLanguageId(int $langId)
    {
        /** @var LanguageInterface $language */
        foreach ($this->languages as $language) {
            if ($langId === $language->getId()) {
                return $this->localeRepoCLDR->getLocale($language->getLocale())->getCurrencyPattern();
            }
        }

        throw new LanguageNotFoundException(new LanguageId($langId));
    }

    /**
     * @param int $currencyId
     * @param int $defaultCurrencyId
     *
     * @throws CannotDeleteDefaultCurrencyException
     */
    protected function assertDefaultCurrencyIsNotBeingRemovedOrDisabled(int $currencyId, int $defaultCurrencyId)
    {
        if ($currencyId === $defaultCurrencyId) {
            throw new CannotDeleteDefaultCurrencyException(sprintf('Currency with id "%s" is the default currency and cannot be deleted or disabled.', $currencyId));
        }
    }

    /**
     * Prevents from removing the currency from any shop context.
     *
     * @param Currency $currency
     *
     * @throws DefaultCurrencyInMultiShopException
     */
    protected function assertDefaultCurrencyIsNotBeingRemovedOrDisabledFromAnyShop(Currency $currency)
    {
        $allShopIds = Shop::getShops(false, null, true);

        foreach ($allShopIds as $shopId) {
            $shopDefaultCurrencyId = (int) Configuration::get(
                'PS_CURRENCY_DEFAULT',
                null,
                null,
                $shopId
            );

            if ((int) $currency->id !== $shopDefaultCurrencyId) {
                continue;
            }

            $shop = new Shop($shopId);
            throw new DefaultCurrencyInMultiShopException($currency->name, $shop->name, sprintf('Currency with id %s cannot be removed or disabled from shop with id %s because its the default currency.', $currency->id, $shopId), DefaultCurrencyInMultiShopException::CANNOT_REMOVE_CURRENCY);
        }
    }
}
