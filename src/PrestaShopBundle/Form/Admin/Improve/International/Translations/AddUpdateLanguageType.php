<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Translations;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AddUpdateLanguageType is responsible for building add / update language form
 * in 'Improve > International > Translations' page.
 */
class AddUpdateLanguageType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $nonInstalledLocalizationChoices;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $nonInstalledLocalizationChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $nonInstalledLocalizationChoices
    ) {
        parent::__construct($translator, $locales);
        $this->nonInstalledLocalizationChoices = $nonInstalledLocalizationChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('iso_localization_pack', ChoiceType::class, [
            'label' => $this->trans('Please select the language you want to add or update', 'Admin.International.Feature'),
            'autocomplete' => true,
            'choices' => [
                $this->trans('Update a language', 'Admin.International.Feature') => $this->getLocaleChoices(),
                $this->trans('Add a language', 'Admin.International.Feature') => $this->nonInstalledLocalizationChoices,
            ],
            'choice_translation_domain' => false,
        ]);
    }
}
