<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Login;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Back-office login form
 */
class LoginType extends AbstractType
{
    private bool $isDemoModeEnabled;

    public function __construct(
        protected readonly TranslatorInterface $translator,
        protected readonly ConfigurationInterface $configuration,
        protected readonly RequestStack $requestStack,
        ShopConfigurationInterface $shopConfiguration,
    ) {
        $this->isDemoModeEnabled = $shopConfiguration->getBoolean('_PS_MODE_DEMO_');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans('Email address', [], 'Admin.Global'),
                'constraints' => [
                    new Email(),
                ],
                'attr' => $this->isDemoModeEnabled ? [
                    // Setting a default value should be in a FormDataProvider, but has been left here to keep consistency with the password
                    'value' => $this->requestStack->getCurrentRequest()->query->get('email'),
                ] : [],
            ])
            ->add('passwd', PasswordType::class, [
                'label' => $this->translator->trans('Password', [], 'Admin.Global'),
                'always_empty' => false,
                'attr' => $this->isDemoModeEnabled ? [
                    // This is the only place we can set a value for a password input field.
                    'value' => $this->requestStack->getCurrentRequest()->query->get('password'),
                ] : [],
            ])
            ->add('submit_login', SubmitType::class, [
                'label' => $this->translator->trans('Log in', [], 'Admin.Login.Feature'),
            ])
            ->add('stay_logged_in', CheckboxType::class, [
                'label' => $this->translator->trans('Stay logged in', [], 'Admin.Login.Feature'),
                'required' => false,
                'external_link' => [
                    'href' => '#forgotten_password',
                    'text' => $this->translator->trans('I forgot my password', [], 'Admin.Login.Feature'),
                    'open_in_new_tab' => false,
                    'attr' => [
                        'id' => 'forgot-password-link',
                        'class' => 'show-forgot-password',
                    ],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->configuration->get('PS_SHOP_NAME'),
            'label_tag_name' => 'h4',
            'form_theme' => '@PrestaShop/Admin/Login/form_theme.html.twig',
            'attr' => [
                'id' => 'login_form',
            ],
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
