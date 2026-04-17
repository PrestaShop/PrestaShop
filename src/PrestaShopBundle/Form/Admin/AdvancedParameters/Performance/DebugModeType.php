<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShopBundle\Form\Admin\Type\GeneratableTextType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form class generates the "Debug mode" form in Performance page.
 */
class DebugModeType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('disable_overrides', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Disable all overrides', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Enable or disable all classes and controllers overrides.', 'Admin.Advparameters.Feature'),
            ])
            ->add('debug_mode', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Debug mode', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Enable or disable debug mode. Debug mode will enable extended error reporting, display the Symfony debug bar, and other features.', 'Admin.Advparameters.Help'),
            ])
            ->add('debug_cookie_name', GeneratableTextType::class, [
                'required' => false,
                'generated_value_length' => 16,
                'label' => $this->trans('Debug cookie name', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('(Optional) Insert a cookie name to enable the debug mode only when this cookie is set.', 'Admin.Advparameters.Help'),
                'row_attr' => [
                    'class' => 'debug-mode-option',
                ],
            ])
            ->add('debug_cookie_value', GeneratableTextType::class, [
                'required' => false,
                'generated_value_length' => 16,
                'label' => $this->trans('Debug cookie value', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('(Optional) Insert a value to enable the debug mode only when the cookie configured above is set to this value.', 'Admin.Advparameters.Help'),
                'row_attr' => [
                    'class' => 'debug-mode-option',
                ],
            ])
            ->add('debug_profiling', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Debug profiler', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Enable or disable debug profiling. Debug profiling will display performance-related information under each page and help find performance bottlenecks in your store.', 'Admin.Advparameters.Help'),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'performance_debug_mode_block';
    }
}
