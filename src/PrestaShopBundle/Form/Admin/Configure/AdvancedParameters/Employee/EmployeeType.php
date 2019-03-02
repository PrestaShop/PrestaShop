<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Employee;

use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\ValueObject\LastName;
use PrestaShopBundle\Form\Admin\Type\ChangePasswordType;
use PrestaShopBundle\Form\Admin\Type\ClickableAvatarType;
use PrestaShopBundle\Form\Admin\Type\AddonsConnectType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EmployeeType defines an employee form.
 */
final class EmployeeType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * @var array
     */
    private $languagesChoices;

    /**
     * @var array
     */
    private $tabChoices;

    /**
     * @var array
     */
    private $profilesChoices;

    /**
     * @var bool
     */
    private $isMultistoreFeatureActive;

    /**
     * @param array $languagesChoices
     * @param array $tabChoices
     * @param array $profilesChoices
     * @param bool $isMultistoreFeatureActive
     */
    public function __construct(
        array $languagesChoices,
        array $tabChoices,
        array $profilesChoices,
        $isMultistoreFeatureActive
    ) {
        $this->languagesChoices = $languagesChoices;
        $this->tabChoices = $tabChoices;
        $this->profilesChoices = $profilesChoices;
        $this->isMultistoreFeatureActive = $isMultistoreFeatureActive;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isRestrictedAccess = $options['is_restricted_access'];

        $builder
            ->add('firstname', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty', [], 'Admin.Notifications.Error'),
                    ]),
                    new Length([
                        'max' => FirstName::MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => FirstName::MAX_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty', [], 'Admin.Notifications.Error'),
                    ]),
                    new Length([
                        'max' => LastName::MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => LastName::MAX_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('avatar', ClickableAvatarType::class)
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty', [], 'Admin.Notifications.Error'),
                    ]),
                    new Email([
                        'message' => $this->trans('This field is invalid', [], 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
        ;

        if ($isRestrictedAccess) {
            $builder
                ->add('change_password', ChangePasswordType::class)
                ->add('prestashop_addons', AddonsConnectType::class, [
                    'label' => $this->trans('Sign in', [], 'Admin.Advparameters.Feature'),
                ]
            );
        } else {
            $builder->add('password', PasswordType::class);
        }

        $builder
            ->add('optin', SwitchType::class, [
                'required' => false,
            ])
            ->add('default_page', ChoiceType::class, [
                'choices' => $this->tabChoices,
            ])
            ->add('language', ChoiceType::class, [
                'choices' => $this->languagesChoices,
            ])
        ;

        if (!$isRestrictedAccess) {
            $builder
                ->add(
                    'active',
                    SwitchType::class,
                    [
                        'required' => false,
                    ]
                )
                ->add(
                    'profile',
                    ChoiceType::class,
                    [
                        'choices' => $this->profilesChoices,
                    ]
                )
            ;

            if ($this->isMultistoreFeatureActive) {
                $builder->add('shop_association', ShopChoiceTreeType::class, [
                    'required' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans('This field cannot be empty', [], 'Admin.Notifications.Error'),
                        ]),
                    ],
                ]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // When is_restricted_access is set to true, the form will show fields differently:
                // - "Change password" field (with regeneration option) shown instead of single password input,
                // - Status switch not shown,
                // - Profile selection not shown,
                // - Addons connect field is shown,
                // - Shop association field is not shown.
                'is_restricted_access' => true,
                'compound' => true,
            ])
            ->setAllowedTypes('is_restricted_access', 'bool')
        ;
    }
}
