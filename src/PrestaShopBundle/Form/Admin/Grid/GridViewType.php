<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Grid;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class GridViewType extends TranslatorAwareType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255]),
                ],
            ])
            ->add('shared', SwitchType::class, [
                'label' => $this->trans('Shared view', 'Admin.Global'),
                'help' => $this->trans('A shared view is visible to the other employees of the shop.', 'Admin.Global'),
                'required' => false,
            ])
        ;

        if (!empty($options['active_date_filters'])) {
            $builder->add('dynamic_date_rules', FormType::class, [
                'label' => false,
                'required' => false,
            ]);

            foreach ($options['active_date_filters'] as $field => $dateFilter) {
                $builder->get('dynamic_date_rules')->add($field, DynamicDateRuleType::class, [
                    'label' => $dateFilter['name'] ?: $field,
                ]);
            }
        }

        if ($options['with_grid_context']) {
            $builder
                ->add('controller_route', HiddenType::class, [
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 255]),
                        new Regex(['pattern' => '/^[a-zA-Z0-9_.]+$/']),
                    ],
                ])
                ->add('filter_id', HiddenType::class, [
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 191]),
                        new Regex(['pattern' => '/^[a-zA-Z0-9_-]+$/']),
                    ],
                ])
                ->add('grid_state', HiddenType::class, [
                    'required' => false,
                    'constraints' => [
                        new Length(['max' => 65000]),
                    ],
                ])
            ;
        }
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'with_grid_context' => true,
            'active_date_filters' => [],
        ]);

        $resolver->setAllowedTypes('with_grid_context', 'bool');
        $resolver->setAllowedTypes('active_date_filters', 'array');
    }
}
