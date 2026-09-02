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
 * Class CopyLanguageType is responsible for building 'Copy' form
 * in 'Improve > International > Translations' page.
 */
class CopyLanguageType extends TranslatorAwareType
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
            ->add('from_language', LocaleChoiceType::class, [
                'label' => 'From',
            ])
            ->add('from_theme', ChoiceType::class, [
                'label' => false,
                'choices' => $this->themeChoices,
                'choice_translation_domain' => false,
            ])
            ->add('to_language', LocaleChoiceType::class, [
                'label' => 'To',
            ])
            ->add('to_theme', ChoiceType::class, [
                'label' => false,
                'choices' => $this->themeChoices,
                'choice_translation_domain' => false,
            ]);
    }
}
