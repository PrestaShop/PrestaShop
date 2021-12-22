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

namespace PrestaShopBundle\Form\Admin\Improve\International\Translations;

use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ThemeProviderDefinition;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

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

        if (isset($this->themeChoices[ThemeProviderDefinition::DEFAULT_THEME_NAME])) {
            $themeChoiceAttributes[ThemeProviderDefinition::DEFAULT_THEME_NAME] = [
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
                'attr' => [
                    'data-minimumResultsForSearch' => '7',
                    'data-toggle' => 'select2',
                ],
                'choices' => $this->moduleChoices,
                'choice_translation_domain' => false,
            ])
            ->add('language', ChoiceType::class, [
                'label' => $this->trans('Select your language', 'Admin.International.Feature'),
                'placeholder' => $this->trans('Language', 'Admin.Global'),
                'choices' => $this->getLocaleChoices(),
                'choice_translation_domain' => false,
            ]);
    }
}
