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
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\International\Translations;

use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ThemeProviderDefinition;
use PrestaShopBundle\Form\Admin\Type\RadioWithChoiceChildrenType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ExportThemeLanguageType is responsible for building export language form
 * in 'Improve > International > Translations' page.
 */
class ExportCataloguesType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $exportTranslationCoreTypeChoices;

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
     * @param array $themeChoices
     * @param array $exportTranslationCoreTypeChoices
     * @param array $moduleChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $exportTranslationCoreTypeChoices,
        array $themeChoices,
        array $moduleChoices
    ) {
        parent::__construct($translator, $locales);
        $this->exportTranslationCoreTypeChoices = $exportTranslationCoreTypeChoices;
        $this->themeChoices = $themeChoices;
        $this->moduleChoices = $moduleChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('iso_code', ChoiceType::class, [
                'label' => $this->trans(
                    'Language',
                    'Admin.Global'
                ),
                'choices' => $this->getLocaleChoices(),
                'choice_translation_domain' => false,
            ]);

        $builder->add('core_selectors', RadioWithChoiceChildrenType::class, [
            'radio_name' => 'core_type',
            'radio_label' => $this->trans('PrestaShop translations', 'Admin.International.Feature'),
            'required' => false,
            'label' => $this->trans('Export', 'Admin.Actions'),
            'child_choice' => [
                'name' => 'selected_value',
                'choices' => $this->exportTranslationCoreTypeChoices,
                'label' => false,
                'multiple' => true,
            ],
        ]);

        $builder->add('themes_selectors', RadioWithChoiceChildrenType::class, [
            'radio_name' => 'themes_type',
            'radio_label' => $this->trans('Theme translations', 'Admin.International.Feature'),
            'required' => false,
            'label' => null,
            'child_choice' => [
                'name' => 'selected_value',
                'empty' => $this->trans('Select a theme', 'Admin.International.Feature'),
                'choices' => $this->excludeDefaultThemeFromChoices($this->themeChoices),
                'label' => false,
                'multiple' => false,
            ],
        ]);

        $builder->add('modules_selectors', RadioWithChoiceChildrenType::class, [
            'radio_name' => 'modules_type',
            'radio_label' => $this->trans('Installed module translations', 'Admin.International.Feature'),
            'required' => false,
            'label' => null,
            'child_choice' => [
                'name' => 'selected_value',
                'empty' => $this->trans('Select a module', 'Admin.International.Feature'),
                'choices' => $this->moduleChoices,
                'label' => false,
                'multiple' => false,
            ],
        ]);
    }

    /**
     * @param array $themeChoices
     *
     * @return array
     */
    private function excludeDefaultThemeFromChoices(array $themeChoices): array
    {
        unset($themeChoices[ThemeProviderDefinition::DEFAULT_THEME_NAME]);

        return $themeChoices;
    }
}
