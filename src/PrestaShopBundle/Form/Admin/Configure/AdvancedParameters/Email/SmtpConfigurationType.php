<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SmtpConfigurationType build form for SMTP data configuration.
 */
class SmtpConfigurationType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domain', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'label' => $this->trans('Email domain name', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Fully qualified domain name (keep this field empty if you don\'t know).', 'Admin.Advparameters.Help'),
                'attr' => [
                    'autocapitalize' => 'off',
                ],
            ])
            ->add('server', TextType::class, [
                'required' => false,
                'label' => $this->trans('SMTP server', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('IP address or server name (e.g. smtp.mydomain.com).', 'Admin.Advparameters.Help'),
                'attr' => [
                    'autocapitalize' => 'off',
                ],
            ])
            ->add('username', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'label' => $this->trans('SMTP username', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Leave blank if not applicable.', 'Admin.Advparameters.Help'),
                'attr' => [
                    'autocapitalize' => 'off',
                ],
            ])
            ->add('password', PasswordType::class, [
                'required' => false,
                'empty_data' => '',
                'label' => $this->trans('SMTP password', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Leave blank if not applicable.', 'Admin.Advparameters.Help'),
                /* Some browsers (for example Google Chrome) are totally ignoring "off" value, so we use "new-password" - which is working well for this purpose */
                'attr' => [
                    'autocomplete' => 'new-password',
                    'autocapitalize' => 'off',
                ],
            ])
            ->add('encryption', ChoiceType::class, [
                'choices' => [
                    'None' => 'off',
                    'TLS' => 'tls',
                ],
                'choice_translation_domain' => 'Admin.Advparameters.Feature',
                'label' => $this->trans('Encryption', 'Admin.Advparameters.Feature'),
            ])
            ->add('port', TextType::class, [
                'required' => false,
                'label' => $this->trans('Port', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Port number to use.', 'Admin.Advparameters.Help'),
            ]);
    }
}
