<?php


namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Import;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImportDataConfigurationType is responsible for displaying the configuration of the
 * Advanced Parameters -> Import -> second step list
 */
class ImportDataConfigurationType extends AbstractType
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
     * @param array $dataMatchChoices
     * @param array $entityFieldChoices
     */
    public function __construct(array $dataMatchChoices, array $entityFieldChoices)
    {
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
                'choice_translation_domain' => false
            ])
            ->add('match_name', TextType::class)
            ->add('rows_skip', IntegerType::class, [
                'data' => 1
            ])
            ->add('available_fields', ChoiceType::class, [
                'choices' => $this->entityFieldChoices,
                'choice_translation_domain' => false,
            ])
        ;
    }
}
