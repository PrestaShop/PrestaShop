<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Translations;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Form\Admin\Type\LocaleChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ModifyTranslationsType is responsible for building 'Modify translations' form
 * in 'Improve > International > Translations' page.
 */
class ModifyTranslationsType extends TranslatorAwareType
{
    public const CORE_TRANSLATIONS_CHOICE_INDEX = '0';
    /**
     * @var array
     */
    private $translationTypeChoices;

    /**
     * @var array
     */
    private $emailContentTypeChoices;

    /**
     * @var array
     */
    private $themeChoices;

    /**
     * @var array
     */
    private $moduleChoices;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $translationTypeChoices
     * @param array $emailContentTypeChoices
     * @param array $themeChoices
     * @param array $moduleChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $translationTypeChoices,
        array $emailContentTypeChoices,
        array $themeChoices,
        array $moduleChoices
    ) {
        parent::__construct($translator, $locales);
        $this->translationTypeChoices = $translationTypeChoices;
        $this->emailContentTypeChoices = $emailContentTypeChoices;
        $this->themeChoices = $themeChoices;
        $this->moduleChoices = $moduleChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $noTheme = $this->trans('Core (no theme selected)', 'Admin.International.Feature');

        $themeChoiceAttributes = [
            $noTheme => [
                'class' => 'js-no-theme',
            ],
        ];

        // Only one theme must be identified as the default one
        if (isset($this->themeChoices[Theme::getDefaultTheme()])) {
            $themeChoiceAttributes[Theme::getDefaultTheme()] = [
                'class' => 'js-default-theme',
            ];
        }

        $builder
            ->add('translation_type', ChoiceType::class, [
                'label' => $this->trans('Type of translation', 'Admin.International.Feature'),
                'attr' => [
                    'class' => 'js-translation-type',
                ],
                'choices' => $this->translationTypeChoices,
                'choice_translation_domain' => false,
            ])
            ->add('email_content_type', ChoiceType::class, [
                'label' => $this->trans('Select the type of email content', 'Admin.International.Feature'),
                'row_attr' => [
                    'class' => 'js-email-form-group d-none',
                ],
                'attr' => [
                    'class' => 'js-email-content-type',
                ],
                'choices' => $this->emailContentTypeChoices,
                'choice_translation_domain' => false,
            ])
            ->add('theme', ChoiceType::class, [
                'label' => $this->trans('Select your theme', 'Admin.International.Feature'),
                'row_attr' => [
                    'class' => 'js-theme-form-group d-none',
                ],
                'choices' => [$noTheme => self::CORE_TRANSLATIONS_CHOICE_INDEX] + $this->themeChoices,
                'choice_attr' => $themeChoiceAttributes,
                'choice_translation_domain' => false,
            ])
            ->add('module', ChoiceType::class, [
                'label' => $this->trans('Select your module', 'Admin.International.Feature'),
                'row_attr' => [
                    'class' => 'js-module-form-group d-none',
                ],
                'placeholder' => '---',
                'autocomplete' => true,
                'choices' => $this->moduleChoices,
                'choice_translation_domain' => false,
            ])
            ->add('language', LocaleChoiceType::class, [
                'label' => $this->trans('Select your language', 'Admin.International.Feature'),
                'placeholder' => $this->trans('Language', 'Admin.Global'),
            ]);
    }
}
