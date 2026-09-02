<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioWithChoiceChildrenType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add($options['radio_name'], RadioType::class, [
            'required' => false,
            'label' => $options['radio_label'],
        ]);

        if (isset($options['child_choice'])) {
            $childChoice = $options['child_choice'];
            $childChoiceAttr = [];
            if (isset($childChoice['empty'])) {
                $childChoice['choices'] = array_merge([$childChoice['empty'] => ''], $childChoice['choices']);
                $childChoiceAttr[$childChoice['empty']] = ['disabled' => true];
            }
            $builder->add($childChoice['name'], ChoiceType::class, [
                'label' => false,
                'row_attr' => [
                    'class' => 'export-translations-child',
                ],
                'autocomplete' => $childChoice['autocomplete'] ?? false,
                'choices' => $childChoice['choices'],
                'choice_attr' => $childChoiceAttr,
                'expanded' => $childChoice['multiple'], // same value as multiple. We can only have Select or Checkboxes
                'multiple' => $childChoice['multiple'],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired([
                'radio_name',
                'radio_label',
                'child_choice',
            ])
        ;

        $resolver->setAllowedTypes('child_choice', 'array');
        $resolver->setAllowedTypes('radio_name', 'string');
        $resolver->setAllowedTypes('radio_label', 'string');
    }
}
