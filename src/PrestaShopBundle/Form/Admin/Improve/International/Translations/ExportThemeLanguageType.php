<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Translations;

use PrestaShopBundle\Form\Admin\Type\LocaleChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ExportThemeLanguageType is responsible for building export language form
 * in 'Improve > International > Translations' page.
 */
class ExportThemeLanguageType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $themeChoices;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $themeChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $themeChoices
    ) {
        parent::__construct($translator, $locales);
        $this->themeChoices = $themeChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('iso_code', LocaleChoiceType::class)
            ->add('theme_name', ChoiceType::class, [
                'label' => $this->trans(
                    'Select your theme',
                    'Admin.International.Feature'
                ),
                'choices' => $this->themeChoices,
                'choice_translation_domain' => false,
            ]);
    }
}
