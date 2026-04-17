<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\International\Translations;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Form\Admin\Type\LocaleChoiceType;
use PrestaShopBundle\Form\Admin\Type\RadioWithChoiceChildrenType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
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
        $builder->add('iso_code', LocaleChoiceType::class);
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
                'choices' => $this->excludeCoreThemesFromChoices($this->themeChoices),
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
                'autocomplete' => true,
            ],
        ]);
    }

    /**
     * @param array $themeChoices
     *
     * @return array
     */
    private function excludeCoreThemesFromChoices(array $themeChoices): array
    {
        foreach (Theme::CORE_THEMES as $coreTheme) {
            if (isset($themeChoices[$coreTheme])) {
                unset($themeChoices[$coreTheme]);
            }
        }

        return $themeChoices;
    }
}
