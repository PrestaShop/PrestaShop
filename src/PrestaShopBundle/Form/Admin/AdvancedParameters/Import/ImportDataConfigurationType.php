<?php


namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Import;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ImportDataConfigurationType is responsible for displaying the configuration of the
 * Advanced Parameters -> Import -> second step list
 */
class ImportDataConfigurationType extends AbstractType
{
    /**
     * @var array
     */
    private $dataMatchChoices;

    /**
     * @param array $dataMatchChoices
     */
    public function __construct(array $dataMatchChoices)
    {
        $this->dataMatchChoices = $dataMatchChoices;
    }

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
        ;
    }
}
