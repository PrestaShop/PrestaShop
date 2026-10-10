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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
            ->add('retention_days', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'multistore_configuration_key' => 'PS_CSP_RETENTION_DAYS',
                'label' => $this->trans('Delete reports older than', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('When the "prestashop:csp:prune-log" command runs, delete reported violations older than the selected age. "Never" keeps them until you clear the log by hand (the per-shop row cap bounds the log but never deletes existing rows). Allowed sources are never deleted. Pruning runs only while Content Security Policy is enabled.', 'Admin.Advparameters.Help'),
                'choices' => [
                    'Never (keep until cleared)' => 0,
                    '7 days' => 7,
                    '14 days' => 14,
                    '30 days' => 30,
                    '90 days' => 90,
                    '180 days' => 180,
                    '365 days' => 365,
                ],
                'choice_translation_domain' => 'Admin.Advparameters.Feature',
            ])
            ->add('report_uri', TextType::class, [
                'required' => false,
                'multistore_configuration_key' => 'PS_CSP_REPORT_URI',
                'label' => $this->trans('External reporting endpoint', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Leave empty to collect reports in PrestaShop. To send this shop\'s violation reports to your own CSP monitoring service instead, enter its URL (https://…); the report log on this page then stays empty. Each report includes the full address of the page where the violation happened, query string included, so on some pages (password reset, order confirmation) it carries tokens or the customer\'s secure key. Only use an endpoint you trust with that data.', 'Admin.Advparameters.Help'),
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
