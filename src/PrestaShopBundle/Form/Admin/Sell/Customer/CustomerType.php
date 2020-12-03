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

namespace PrestaShopBundle\Form\Admin\Sell\Customer;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CustomerName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Type is used to created form for customer add/edit actions
 */
class CustomerType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * @var array
     */
    private $genderChoices;

    /**
     * @var array
     */
    private $groupChoices;

    /**
     * @var bool
     */
    private $isB2bFeatureEnabled;

    /**
     * @var array
     */
    private $riskChoices;

    /**
     * @var bool
     */
    private $isPartnerOffersEnabled;

    /**
     * @param array $genderChoices
     * @param array $groupChoices
     * @param array $riskChoices
     * @param bool $isB2bFeatureEnabled
     * @param bool $isPartnerOffersEnabled
     */
    public function __construct(
        array $genderChoices,
        array $groupChoices,
        array $riskChoices,
        $isB2bFeatureEnabled,
        $isPartnerOffersEnabled
    ) {
        $this->genderChoices = $genderChoices;
        $this->groupChoices = $groupChoices;
        $this->isB2bFeatureEnabled = $isB2bFeatureEnabled;
        $this->riskChoices = $riskChoices;
        $this->isPartnerOffersEnabled = $isPartnerOffersEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gender_id', ChoiceType::class, [
                'choices' => $this->genderChoices,
                'multiple' => false,
                'expanded' => true,
                'required' => false,
                'placeholder' => null,
            ])
            ->add('first_name', TextType::class, [
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
                    new CustomerName([
                        'message' => $this->trans('The %s field is invalid.', [sprintf('"%s"', $this->trans('First name', [], 'Admin.Global'))], 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('last_name', TextType::class, [
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
                    new CustomerName([
                        'message' => $this->trans('The %s field is invalid.', [sprintf('"%s"', $this->trans('Last name', [], 'Admin.Global'))], 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
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
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new Length([
                        'max' => Password::MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => Password::MAX_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                        'min' => Password::MIN_LENGTH,
                        'minMessage' => $this->trans(
                            'This field cannot be shorter than %limit% characters',
                            ['%limit%' => Password::MIN_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'required' => $options['is_password_required'],
            ])
            ->add('birthday', BirthdayType::class, [
                'required' => false,
                'format' => 'yyyy MM dd',
                'input' => 'string',
            ])
            ->add('is_enabled', SwitchType::class, [
                'required' => false,
            ])
            ->add('is_partner_offers_subscribed', SwitchType::class, [
                'required' => false,
                'disabled' => !$this->isPartnerOffersEnabled,
            ])
            ->add('group_ids', MaterialChoiceTableType::class, [
                'empty_data' => [],
                'choices' => $this->groupChoices,
            ])
            ->add('default_group_id', ChoiceType::class, [
                'required' => false,
                'placeholder' => null,
                'choices' => $this->groupChoices,
            ])
        ;

        if ($this->isB2bFeatureEnabled) {
            $builder
                ->add('company_name', TextType::class, [
                    'required' => false,
                ])
                ->add('siret_code', TextType::class, [
                    'required' => false,
                ])
                ->add('ape_code', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Type([
                            'type' => 'alnum',
                            'message' => $this->trans('This field is invalid', [], 'Admin.Notifications.Error'),
                        ]),
                    ],
                ])
                ->add('website', TextType::class, [
                    'required' => false,
                ])
                ->add('allowed_outstanding_amount', NumberType::class, [
                    'scale' => 6,
                    'required' => false,
                    'invalid_message' => $this->trans('This field is invalid', [], 'Admin.Notifications.Error'),
                ])
                ->add('max_payment_days', IntegerType::class, [
                    'required' => false,
                    'invalid_message' => $this->trans('This field is invalid', [], 'Admin.Notifications.Error'),
                ])
                ->add('risk_id', ChoiceType::class, [
                    'required' => false,
                    'placeholder' => null,
                    'choices' => $this->riskChoices,
                ])
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // password is configurable
                // so it may be optional when editing customer
                'is_password_required' => true,
            ])
            ->setAllowedTypes('is_password_required', 'bool')
        ;
    }
}
