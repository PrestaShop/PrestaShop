<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Import;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ImportDataConfigurationType is responsible for displaying the configuration of the
 * Advanced Parameters -> Import -> second step list.
 */
class ImportDataConfigurationType extends TranslatorAwareType
{
    /**
     * @var array choices for data matches
     */
    private $dataMatchChoices;

    /**
     * @var array choices for entity fields
     */
    private $entityFieldChoices;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $dataMatchChoices
     * @param array $entityFieldChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $dataMatchChoices,
        array $entityFieldChoices
    ) {
        parent::__construct($translator, $locales);

        $this->dataMatchChoices = $dataMatchChoices;
        $this->entityFieldChoices = $entityFieldChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('matches', ChoiceType::class, [
                'choices' => $this->dataMatchChoices,
                'choice_translation_domain' => false,
            ])
            ->add('match_name', TextType::class, [
                'required' => false,
            ])
            ->add('skip', IntegerType::class, [
                'data' => 1,
            ])
            ->add('type_value', CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'entry_options' => [
                    'choices' => [
                        $this->trans('Ignore this column', 'Admin.Advparameters.Feature') => 'no',
                    ] +
                    $this->entityFieldChoices,
                    'choice_translation_domain' => false,
                    'label' => false,
                ],
                'label' => false,
            ])
            ->add('entity', HiddenType::class)
            ->add('csv', HiddenType::class)
            ->add('iso_lang', HiddenType::class)
            ->add('truncate', HiddenType::class)
            ->add('match_ref', HiddenType::class)
            ->add('regenerate', HiddenType::class)
            ->add('forceIDs', HiddenType::class)
            ->add('sendemail', HiddenType::class)
            ->add('separator', HiddenType::class)
            ->add('multiple_value_separator', HiddenType::class);
    }
}
