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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Url;

/** Settings block of the "Advanced parameters > Security > Content Security Policy" page. */
final class CspType extends TranslatorAwareType
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
                'help' => $this->trans('Only applies when Content Security Policy is enabled above. On: violations are reported but nothing is blocked. Off: the policy is enforced and any source that is not on your allow-list is blocked in visitors\' browsers. Turn this off only once the allow-list is complete, or the storefront may break.', 'Admin.Advparameters.Help'),
            ])
            ->add('retention_days', IntegerType::class, [
                'required' => false,
                'multistore_configuration_key' => 'PS_CSP_RETENTION_DAYS',
                'label' => $this->trans('Delete reports older than (days)', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('When the "prestashop:csp:prune-log" command runs, delete reported violations older than this many days. 0 keeps them until the row cap evicts them. Allowed sources are never deleted. Pruning runs only while Content Security Policy is enabled.', 'Admin.Advparameters.Help'),
                'constraints' => [
                    new GreaterThanOrEqual(0),
                ],
                'attr' => ['min' => 0],
            ])
            ->add('report_uri', TextType::class, [
                'required' => false,
                'multistore_configuration_key' => 'PS_CSP_REPORT_URI',
                'label' => $this->trans('External reporting endpoint', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Leave empty to collect reports in PrestaShop. To send this shop\'s violation reports to your own CSP monitoring service instead, enter its URL (https://…); the report log on this page then stays empty.', 'Admin.Advparameters.Help'),
                'constraints' => [
                    new Url(['protocols' => ['http', 'https']]),
                ],
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
