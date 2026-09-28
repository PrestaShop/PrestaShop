<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Csp;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Generates the settings block of the
 * "Advanced parameters > Security > Content Security Policy" page.
 */
class CspType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', SwitchType::class, [
                'required' => false,
                'multistore_configuration_key' => 'PS_CSP_ENABLED',
                'label' => $this->trans('Enable Content Security Policy', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Send a Content-Security-Policy header on every storefront page of this shop.', 'Admin.Advparameters.Help'),
            ])
            ->add('report_only', SwitchType::class, [
                'required' => false,
                'multistore_configuration_key' => 'PS_CSP_REPORT_ONLY',
                'label' => $this->trans('Report-only mode', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Only report violations without blocking anything. Keep this on until your allow-list is complete.', 'Admin.Advparameters.Help'),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Advparameters.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'csp_options_block';
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationType
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
