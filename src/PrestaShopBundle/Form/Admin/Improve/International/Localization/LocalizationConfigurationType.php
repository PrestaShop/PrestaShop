<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Entity\Repository\TimezoneRepository;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LocalizationConfigurationType is responsible for building 'Improve > International > Localization' page
 * 'Configuration' form
 */
class LocalizationConfigurationType extends AbstractType
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var LanguageDataProvider
     */
    private $languageDataProvider;

    /**
     * @var CountryDataProvider
     */
    private $countryDataProvider;

    /**
     * @var CurrencyDataProvider
     */
    private $currencyDataProvider;

    /**
     * @var TimezoneRepository
     */
    private $timezoneRepository;

    /**
     * @param LegacyContext $legacyContext
     * @param LanguageDataProvider $languageDataProvider
     * @param CountryDataProvider $countryDataProvider
     * @param CurrencyDataProvider $currencyDataProvider
     * @param TimezoneRepository $timezoneRepository
     */
    public function __construct(
        LegacyContext $legacyContext,
        LanguageDataProvider $languageDataProvider,
        CountryDataProvider $countryDataProvider,
        CurrencyDataProvider $currencyDataProvider,
        TimezoneRepository $timezoneRepository
    ) {
        $this->languageDataProvider = $languageDataProvider;
        $this->countryDataProvider = $countryDataProvider;
        $this->legacyContext = $legacyContext;
        $this->currencyDataProvider = $currencyDataProvider;
        $this->timezoneRepository = $timezoneRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('default_language', ChoiceType::class, [
                'choices' => $this->getLanguageChoices(),
            ])
            ->add('detect_language_from_browser', SwitchType::class)
            ->add('default_country', ChoiceType::class, [
                'choices' => $this->getCountryChoices(),
            ])
            ->add('detect_country_from_browser', SwitchType::class)
            ->add('default_currency', ChoiceType::class, [
                'choices' => $this->getCurrencyChoices(),
            ])
            ->add('timezone', ChoiceType::class, [
                'choices' => $this->getTimezoneChoices(),
            ])
        ;
    }

    /**
     * Get available language choices
     *
     * @return array
     */
    private function getLanguageChoices()
    {
        $languages = $this->languageDataProvider->getLanguages();
        $choices = [];

        foreach ($languages as $language) {
            $choices[$language['name']] = $language['id_lang'];
        }

        return $choices;
    }

    /**
     * Get available country choices
     */
    private function getCountryChoices()
    {
        $contextLanguage = $this->legacyContext->getLanguage();
        $countries = $this->countryDataProvider->getCountries($contextLanguage->id);
        $choices = [];

        foreach ($countries as $country) {
            $choices[$country['name']] = $country['id_country'];
        }

        return $choices;
    }

    /**
     * Get available currency choices
     *
     * @return array
     */
    private function getCurrencyChoices()
    {
        $currencies = $this->currencyDataProvider->getCurrencies(
            $asObjects = false,
            $onlyActive = true,
            $group = true
        );
        $choices = [];

        foreach ($currencies as $currency) {
            $choices[$currency['name']] = $currency['id_currency'];
        }

        return $choices;
    }

    /**
     * Get timezone choices
     *
     * @return array
     */
    private function getTimezoneChoices()
    {
        $timezones = $this->timezoneRepository->findAll();
        $choices = [];

        foreach ($timezones as $timezone) {
            $choices[$timezone['name']] = $timezone['name'];
        }

        return $choices;
    }
}
