<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Login;

use PrestaShopBundle\Form\Admin\Type\ButtonCollectionType;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestPasswordResetType extends AbstractType
{
    public function __construct(
        protected readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email_forgot', EmailType::class, [
                'required' => true,
                'label' => $this->translator->trans('Email address', [], 'Admin.Global'),
                'constraints' => [
                    new Email(),
                ],
            ])
            ->add('buttons', ButtonCollectionType::class, [
                'buttons' => [
                    'cancel' => [
                        'type' => ButtonType::class,
                        'options' => [
                            'label' => $this->translator->trans('Cancel', [], 'Admin.Global'),
                        ],
                    ],
                    'submit_login' => [
                        'type' => SubmitType::class,
                        'options' => [
                            'label' => $this->translator->trans('Send reset link', [], 'Admin.Login.Feature'),
                        ],
                        'group' => 'right',
                    ],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->translator->trans('Forgot your password?', [], 'Admin.Login.Feature'),
            'label_tag_name' => 'h4',
            'form_theme' => '@PrestaShop/Admin/Login/form_theme.html.twig',
            'attr' => [
                'id' => 'forgot_password_form',
            ],
        ]);
    }
}
