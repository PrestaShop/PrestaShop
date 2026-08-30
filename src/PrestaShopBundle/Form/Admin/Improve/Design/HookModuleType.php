<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\Design;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class HookModuleType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_module', ChoiceType::class, [
                'label' => $this->trans('Module', 'Admin.Design.Feature'),
                'choices' => $options['module_choices'],
                'placeholder' => $this->trans('-- Select a module --', 'Admin.Design.Feature'),
                'attr' => [
                    'data-module-selector' => 'true',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Module', 'Admin.Design.Feature'))]
                        ),
                    ]),
                ],
            ])
            ->add('id_hook', ChoiceType::class, [
                'label' => $this->trans('Hook', 'Admin.Design.Feature'),
                'choices' => $options['hook_choices'],
                'placeholder' => empty($options['hook_choices'])
                    ? $this->trans('Select a module above before choosing from available hooks', 'Admin.Design.Help')
                    : $this->trans('-- Select a hook --', 'Admin.Design.Feature'),
                'attr' => array_filter([
                    'data-hook-selector' => 'true',
                    'disabled' => empty($options['hook_choices']) ? 'disabled' : null,
                ]),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Hook', 'Admin.Design.Feature'))]
                        ),
                    ]),
                ],
            ])
            ->add('exceptions', TextType::class, [
                'label' => $this->trans('Except pages', 'Admin.Design.Feature'),
                'help' => $this->trans(
                    'Indicate page file names for which the module will not be displayed (e.g., "product, category"). Separate them with commas.',
                    'Admin.Design.Help'
                ),
                'required' => false,
            ])
            ->add('id_hook_original', HiddenType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'module_choices' => [],
            'hook_choices' => [],
            'allow_extra_fields' => true,
        ]);

        $resolver->setAllowedTypes('module_choices', 'array');
        $resolver->setAllowedTypes('hook_choices', 'array');
    }
}
