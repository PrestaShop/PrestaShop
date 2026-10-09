<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Csp;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;

/** Back-office settings block of the Content Security Policy page. Global (not per shop), so no multistore wrapper. */
final class AdminCspType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Enable Content Security Policy', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Send a Content-Security-Policy header on every back-office page.', 'Admin.Advparameters.Help'),
            ])
            ->add('report_only', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Report-only mode', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Only applies when Content Security Policy is enabled above. On: violations are reported but nothing is blocked. Off: the policy is enforced and any source not on your allow-list is blocked. Turn this off only once the allow-list is complete, or the back office may break.', 'Admin.Advparameters.Help'),
            ])
            ->add('retention_days', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'label' => $this->trans('Delete reports older than', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('When the "prestashop:csp:prune-log" command runs, delete reported violations older than the selected age. "Never" keeps them until the row cap evicts them. Allowed sources are never deleted. Pruning runs only while Content Security Policy is enabled.', 'Admin.Advparameters.Help'),
                'choices' => [
                    'Never (keep until the cap)' => 0,
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
                'label' => $this->trans('External reporting endpoint', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Leave empty to collect reports in PrestaShop. To send back-office violation reports to your own CSP monitoring service instead, enter its URL (https://…); the report log on this page then stays empty.', 'Admin.Advparameters.Help'),
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
        return 'admin_csp_options_block';
    }
}
