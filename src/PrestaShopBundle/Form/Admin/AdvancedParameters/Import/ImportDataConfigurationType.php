<?php


namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Import;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
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
            ->add('data_matching_configuration', TextType::class, []);
    }
}
