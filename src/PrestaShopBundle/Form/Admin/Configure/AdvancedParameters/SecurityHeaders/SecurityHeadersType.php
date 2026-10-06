<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\SecurityHeaders;

use PrestaShop\PrestaShop\Adapter\SecurityHeader\SecurityHeadersProvider;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

/** Static security headers block (everything except CSP). Global settings, applied to both surfaces. */
final class SecurityHeadersType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $disabled = $this->trans('Disabled', 'Admin.Global');

        $builder
            ->add('nosniff', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('X-Content-Type-Options: nosniff', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Stops browsers from MIME-sniffing a response away from its declared content type.', 'Admin.Advparameters.Help'),
            ])
            ->add('frame_options', ChoiceType::class, [
                'required' => false,
                'label' => $this->trans('X-Frame-Options', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Protects against clickjacking by controlling whether the site may be framed. SAMEORIGIN is recommended.', 'Admin.Advparameters.Help'),
                'choices' => [
                    'SAMEORIGIN' => 'SAMEORIGIN',
                    'DENY' => 'DENY',
                    $disabled => '',
                ],
            ])
            ->add('referrer_policy', ChoiceType::class, [
                'required' => false,
                'label' => $this->trans('Referrer-Policy', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Controls how much referrer information is sent with requests.', 'Admin.Advparameters.Help'),
                'choices' => array_merge(
                    array_combine(SecurityHeadersProvider::REFERRER_POLICIES, SecurityHeadersProvider::REFERRER_POLICIES),
                    [$disabled => '']
                ),
            ])
            ->add('hsts', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Strict-Transport-Security (HSTS)', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Forces HTTPS for future visits. Only sent over HTTPS. Enable this only once your whole store (and, if you tick "Include subdomains", every subdomain) is served over HTTPS, or visitors may be locked out.', 'Admin.Advparameters.Help'),
            ])
            ->add('hsts_max_age', IntegerType::class, [
                'required' => false,
                'label' => $this->trans('HSTS max-age (seconds)', 'Admin.Advparameters.Feature'),
                'constraints' => [new GreaterThanOrEqual(0)],
                'attr' => ['min' => 0],
            ])
            ->add('hsts_subdomains', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('HSTS: include subdomains', 'Admin.Advparameters.Feature'),
            ])
            ->add('hsts_preload', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('HSTS: preload', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Request inclusion in browser preload lists. Hard to undo, so only turn it on when you are sure.', 'Admin.Advparameters.Help'),
            ])
            ->add('permissions_policy', TextType::class, [
                'required' => false,
                'label' => $this->trans('Permissions-Policy', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Disables browser features by default. Leave empty to send no header. Example: camera=(), microphone=(), geolocation=()', 'Admin.Advparameters.Help'),
                'empty_data' => '',
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
        return 'security_headers_block';
    }
}
