<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
