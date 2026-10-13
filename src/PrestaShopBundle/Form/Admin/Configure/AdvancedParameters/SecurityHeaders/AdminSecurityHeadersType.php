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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Back-office static security headers block. Global (not per shop), so no multistore wrapper. These ship
 * with secure defaults; HSTS is not here because it is per-host and shared from the storefront settings.
 */
final class AdminSecurityHeadersType extends TranslatorAwareType
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
                'help' => $this->trans('Protects against clickjacking by controlling whether the back office may be framed. SAMEORIGIN is recommended (DENY would also block the back office\'s own same-origin frames).', 'Admin.Advparameters.Help'),
                'choices' => [
                    'SAMEORIGIN' => 'SAMEORIGIN',
                    'DENY' => 'DENY',
                    $disabled => '',
                ],
            ])
            ->add('referrer_policy', ChoiceType::class, [
                'required' => false,
                'label' => $this->trans('Referrer-Policy', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('How much referrer information back-office requests send. strict-origin keeps the origin but strips the path, so the admin folder and CSRF token do not leak in the Referer header.', 'Admin.Advparameters.Help'),
                'choices' => array_merge(
                    array_combine(SecurityHeadersProvider::REFERRER_POLICIES, SecurityHeadersProvider::REFERRER_POLICIES),
                    [$disabled => '']
                ),
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
        return 'admin_security_headers_block';
    }
}
