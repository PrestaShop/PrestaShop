<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form class generates the "Smarty" form in Performance page.
 */
class SmartyType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('template_compilation', ChoiceType::class, [
                'choices' => [
                    'Never recompile template files' => 0,
                    'Recompile templates if the files have been updated' => 1,
                    'Force compilation' => 2,
                ],
                'placeholder' => false,
                'required' => false,
                'label' => $this->trans('Template compilation', 'Admin.Advparameters.Feature'),
                'choice_translation_domain' => 'Admin.Advparameters.Feature',
            ])
            ->add('cache', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Cache', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Should be enabled except for debugging.', 'Admin.Advparameters.Feature'),
            ])
            ->add('multi_front_optimization', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Multi-front optimizations', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Should be enabled if you want to avoid to store the smarty cache on NFS.', 'Admin.Advparameters.Help'),
                'row_attr' => [
                    'class' => 'smarty-cache-option',
                ],
            ])
            ->add('clear_cache', ChoiceType::class, [
                'choices' => [
                    'Never clear cache files' => 'never',
                    'Clear cache everytime something has been modified' => 'everytime',
                ],
                'placeholder' => false,
                'required' => false,
                'label' => $this->trans('Clear cache', 'Admin.Advparameters.Feature'),
                'row_attr' => [
                    'class' => 'smarty-cache-option',
                ],
                'choice_translation_domain' => 'Admin.Advparameters.Feature',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'performance_smarty_block';
    }
}
