<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Employee;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email as EmployeeEmail;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use PrestaShopBundle\Form\Admin\Type\ChangePasswordType;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Service\Routing\Router;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
     * @var int
     */
    private $superAdminProfileId;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param array $languagesChoices
     * @param array $tabChoices
     * @param array $profilesChoices
     * @param bool $isMultistoreFeatureActive
     * @param ConfigurationInterface $configuration
     * @param int $superAdminProfileId
     * @param Router $router
     */
    public function __construct(
        array $languagesChoices,
        array $tabChoices,
        array $profilesChoices,
        bool $isMultistoreFeatureActive,
        ConfigurationInterface $configuration,
        int $superAdminProfileId,
        Router $router
    ) {
        $this->languagesChoices = $languagesChoices;
        $this->tabChoices = $tabChoices;
        $this->profilesChoices = $profilesChoices;
        $this->isMultistoreFeatureActive = $isMultistoreFeatureActive;
        $this->configuration = $configuration;
        $this->superAdminProfileId = $superAdminProfileId;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $minScore = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE);
        $maxLength = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH);
        $minLength = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH);

        $builder
            ->add('firstname', TextType::class, [
                'label' => $this->trans('First name', [], 'Admin.Global'),
                'constraints' => [
                    $this->getNotBlankConstraint(),
                    $this->getLengthConstraint(FirstName::MAX_LENGTH),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => $this->trans('Last name', [], 'Admin.Global'),
                'constraints' => [
                    $this->getNotBlankConstraint(),
                    $this->getLengthConstraint(LastName::MAX_LENGTH),
                ],
            ])
            ->add('avatarUrl', FileType::class, [
                'block_prefix' => 'avatar_url',
                'label' => $this->trans('Avatar', [], 'Admin.Global'),
                'required' => false,
                'attr' => [
                    'accept' => 'gif,jpg,jpeg,jpe,png',
                ],
            ])
            ->add('has_enabled_gravatar', SwitchType::class, [
                'label' => $this->trans('Enable gravatar', [], 'Admin.Global'),
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => $this->trans('Email address', [], 'Admin.Global'),
                'constraints' => [
                    $this->getNotBlankConstraint(),
                    $this->getLengthConstraint(EmployeeEmail::MAX_LENGTH),
                    new Email([
                        'message' => $this->trans('This field is invalid', [], 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('change_password', ChangePasswordType::class, [
                'block_prefix' => 'change_password',
            ])
            ->add('password', PasswordType::class, [
                'label' => $this->trans('Password', [], 'Admin.Global'),
                'help' => $this->trans(
                    'Password should be at least %num% characters long.',
                    [
                        '%num%' => 8,
                    ],
                    'Admin.Advparameters.Help'
                ),
                'required' => !$options['is_for_editing'],
                'attr' => [
                    'data-minscore' => $minScore,
                    'data-minlength' => $minLength,
                    'data-maxlength' => $maxLength,
                ],
                'constraints' => [
                    new Length(
                        [
                            'max' => $maxLength,
                            'maxMessage' => $this->getMaxLengthValidationMessage($maxLength),
                            'min' => $minLength,
                            'minMessage' => $this->getMinLengthValidationMessage($minLength),
                        ]
                    ),
                ],
            ])
            ->add('language', ChoiceType::class, [
                'label' => $this->trans('Language', [], 'Admin.Global'),
                'choices' => $this->languagesChoices,
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Active', [], 'Admin.Global'),
                'help' => $this->trans(
                    'Allow or deny this employee\'s access to the Admin panel.',
                    [],
                    'Admin.Advparameters.Help'
                ),
                'required' => false,
            ])
            ->add('profile', ChoiceType::class, [
                'label' => $this->trans('Permission profile', [], 'Admin.Advparameters.Feature'),
                'attr' => [
                    'data-admin-profile' => $this->superAdminProfileId,
                    'data-get-tabs-url' => $this->router->generate('admin_employees_get_tabs'),
                ],
                'choices' => $this->profilesChoices,
            ])
            ->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Store association', [], 'Admin.Global'),
                'help' => $this->trans(
                    'Select the stores the employee is allowed to access.',
                    [],
                    'Admin.Advparameters.Help'
                ),
                'required' => false,
            ])
            ->add('default_page', ChoiceType::class, [
                'label' => $this->trans('Default page', [], 'Admin.Advparameters.Feature'),
                'help' => $this->trans(
                    'This page will be displayed just after login.',
                    [],
                    'Admin.Advparameters.Help'
                ),
                'attr' => [
                    'data-minimumResultsForSearch' => '7',
                    'data-toggle' => 'select2',
                ],
                'choices' => $this->tabChoices,
            ])
        ;

        if ($options['is_restricted_access']) {
            $builder
                ->remove('password')
                ->remove('active')
                ->remove('profile')
                ->remove('shop_association')
            ;
        } else {
            $builder
                ->remove('change_password')
            ;
            if (!$this->isMultistoreFeatureActive) {
                $builder
                    ->remove('shop_association')
                ;
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
                // - Shop association field is not shown.
                'is_restricted_access' => false,

                // Is this form used for editing the employee.
                'is_for_editing' => false,
            ])
            ->setAllowedTypes('is_restricted_access', 'bool')
            ->setAllowedTypes('is_for_editing', 'bool')
        ;
    }

    /**
     * @param int $maxLength
     * @param int|null $minLength
     *
     * @return Length
     */
    private function getLengthConstraint(int $maxLength, ?int $minLength = null): Length
    {
        $options = [
            'max' => $maxLength,
            'maxMessage' => $this->trans(
                'This field cannot be longer than %limit% characters',
                ['%limit%' => $maxLength],
                'Admin.Notifications.Error'
            ),
        ];

        if (null !== $minLength) {
            $options['min'] = $minLength;
            $options['minMessage'] = $this->trans(
                'This field cannot be shorter than %limit% characters',
                ['%limit%' => $minLength],
                'Admin.Notifications.Error'
            );
        }

        return new Length($options);
    }

    /**
     * @param int $minLength
     *
     * @return string
     */
    private function getMinLengthValidationMessage(int $minLength): string
    {
        return $this->trans(
            'This field cannot be shorter than %limit% characters',
            ['%limit%' => $minLength],
            'Admin.Notifications.Error'
        );
    }

    /**
     * @param int $maxLength
     *
     * @return string
     */
    private function getMaxLengthValidationMessage(int $maxLength): string
    {
        return $this->trans(
            'This field cannot be longer than %limit% characters',
            ['%limit%' => $maxLength],
            'Admin.Notifications.Error'
        );
    }

    /**
     * @return NotBlank
     */
    private function getNotBlankConstraint(): NotBlank
    {
        return new NotBlank([
            'message' => $this->trans('This field cannot be empty.', [], 'Admin.Notifications.Error'),
        ]);
    }
}
