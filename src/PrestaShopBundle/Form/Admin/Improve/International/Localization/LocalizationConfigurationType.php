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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\CurrencyChoiceType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LocalizationConfigurationType is responsible for building 'Improve > International > Localization' page
 * 'Configuration' form.
 */
class LocalizationConfigurationType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $languageChoices;

    /**
     * @var array
     */
    private $timezoneChoices;

    /**
     * @param array $languageChoices
     * @param array $timezoneChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $languageChoices,
        array $timezoneChoices
    ) {
        parent::__construct($translator, $locales);
        $this->languageChoices = $languageChoices;
        $this->timezoneChoices = $timezoneChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('default_language', ChoiceType::class, [
                'label' => $this->trans(
                    'Default language',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'The default language used in your shop.',
                    'Admin.International.Help'
                ),
                'choices' => $this->languageChoices,
                'choice_translation_domain' => false,
                'autocomplete' => true,
            ])
            ->add('detect_language_from_browser', SwitchType::class, [
                'label' => $this->trans(
                    'Set language from browser',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'Set browser language as default language.',
                    'Admin.International.Help'
                ),
            ])
            ->add('default_country', CountryChoiceType::class, [
                'label' => $this->trans(
                    'Default country',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'The default country used in your shop.',
                    'Admin.International.Help'
                ),
                'autocomplete' => true,
            ])
            ->add('detect_country_from_browser', SwitchType::class, [
                'label' => $this->trans(
                    'Set default country from browser language',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'Set country corresponding to browser language.',
                    'Admin.International.Help'
                ),
            ]
            )
            ->add('default_currency', CurrencyChoiceType::class, [
                'label' => $this->trans(
                    'Default currency',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'The default currency used in your shop.',
                    'Admin.International.Help'
                ),
                'autocomplete' => true,
                'attr' => [
                    'data-warning-message' => 'Before changing the default currency, we strongly recommend that you enable maintenance mode. Indeed, any change on the default currency requires a manual adjustment of the price of each product and its combinations.',
                ],
            ])
            ->add('timezone', ChoiceType::class, [
                'label' => $this->trans(
                    'Time zone',
                    'Admin.International.Feature'
                ),
                'choices' => $this->timezoneChoices,
                'choice_translation_domain' => false,
                'autocomplete' => true,
            ]);
    }
}
