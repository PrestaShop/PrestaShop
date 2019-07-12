<?php

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTreeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Displays button which has category tree pre-rendered.
 */
class CategoryChoiceTreeButtonType extends AbstractType
{
    /**
     * @var array
     */
    private $categoryTreeChoices;

    /**
     * @param array $categoryTreeChoices
     */
    public function __construct(array $categoryTreeChoices)
    {
        $this->categoryTreeChoices = $categoryTreeChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices_tree' => $this->categoryTreeChoices,
            'choice_label' => 'name',
            'choice_value' => 'id_category',
            'display_unselect_all' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return MaterialChoiceTreeType::class;
    }
}
