<?php


namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Import;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
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
     * @var FormChoiceProviderInterface
     */
    private $dataMatchChoiceProvider;

    public function __construct(FormChoiceProviderInterface $dataMatchChoiceProvider)
    {
        $this->dataMatchChoiceProvider = $dataMatchChoiceProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('matches', ChoiceType::class, [
                'choices' => $this->dataMatchChoiceProvider->getChoices(),
                'choice_translation_domain' => false
            ])
            ->add('match_name', TextType::class)
            ->add('rows_skip', IntegerType::class, [
                'data' => 1
            ]);
    }
}
