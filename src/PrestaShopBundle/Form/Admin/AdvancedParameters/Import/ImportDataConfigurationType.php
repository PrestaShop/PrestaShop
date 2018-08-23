<?php


namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Import;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ImportDataConfigurationType is responsible for displaying the configuration of the
 * Advanced Parameters -> Import -> second step list
 */
class ImportDataConfigurationType extends CommonAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('matches', ChoiceType::class, [
                'choice_translation_domain' => false
            ])
            ->add('match_name', TextType::class)
            ->add('rows_skip', IntegerType::class, [
                'data' => 1
            ]);
    }
}
