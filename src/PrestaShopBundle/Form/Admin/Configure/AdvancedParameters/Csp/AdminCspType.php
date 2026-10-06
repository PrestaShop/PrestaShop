<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Csp;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

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
                'help' => $this->trans('On: violations are reported but nothing is blocked. Off: the policy is enforced and any source not on your allow-list is blocked. Turn this off only once the allow-list is complete, or the back office may break.', 'Admin.Advparameters.Help'),
            ])
            ->add('retention_days', IntegerType::class, [
                'required' => false,
                'label' => $this->trans('Delete reports older than (days)', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('When the "prestashop:csp:prune-log" command runs, delete reported violations older than this many days. 0 keeps them until the row cap evicts them. Allowed sources are never deleted.', 'Admin.Advparameters.Help'),
                'constraints' => [
                    new GreaterThanOrEqual(0),
                ],
                'attr' => ['min' => 0],
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
