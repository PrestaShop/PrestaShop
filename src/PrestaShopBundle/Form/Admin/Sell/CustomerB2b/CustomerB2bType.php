<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\CustomerB2b;

use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\ValueObject\ExternalReference;
use PrestaShopBundle\Entity\Enum\CustomerB2bStatus;
use PrestaShopBundle\Form\Admin\Sell\Customer\CustomerBasicDataType;
use PrestaShopBundle\Form\Admin\Type\B2bRoleChoiceType;
use PrestaShopBundle\Form\Admin\Type\BusinessEntityChoiceType;
use PrestaShopBundle\Form\Admin\Type\CustomerSearchType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CustomerB2bType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($isCreation = !$options['is_edit']) {
            $builder->add('use_existing_customer', SwitchType::class, [
                'label' => $this->translator->trans('Use an existing customer', [], 'Admin.Orderscustomers.Feature'),
                'required' => false,
                'attr' => [
                    'class' => 'js-use-existing-customer-choice',
                ],
            ]);
        }

        $builder->add('customer', CustomerBasicDataType::class, [
            'label' => false,
            'validation_groups' => $isCreation
                ? static fn (FormInterface $form): array => self::usesExistingCustomer($form) ? [] : [Constraint::DEFAULT_GROUP]
                : null,
        ]);

        if ($isCreation) {
            $builder
                ->add('send_password_setup_link', SwitchType::class, [
                    'label' => $this->translator->trans('Send a password reset link', [], 'Admin.Orderscustomers.Feature'),
                    'label_help_box' => $this->translator->trans('The customer receives an email with a link to set up their password. Otherwise, they can set it with the "Forgot your password?" link on the login page.', [], 'Admin.Orderscustomers.Help'),
                    'required' => false,
                ])
                ->add('existing_customer', CustomerSearchType::class, [
                    'label' => $this->translator->trans('Customer', [], 'Admin.Global'),
                    'exclude_guests' => true,
                    'disabling_switch' => false,
                    'error_bubbling' => false,
                    'required' => true,
                    'constraints' => new Count([
                        'min' => 1,
                        'minMessage' => $this->translator->trans('This field cannot be empty.', [], 'Admin.Notifications.Error'),
                    ]),
                    'validation_groups' => static fn (FormInterface $form): array => self::usesExistingCustomer($form) ? [Constraint::DEFAULT_GROUP] : [],
                ])
                ->add('business_entity_id', BusinessEntityChoiceType::class, [
                    'label' => $this->translator->trans('Link to a business entity', [], 'Admin.Orderscustomers.Feature'),
                    'placeholder' => $this->translator->trans('Select an existing business entity', [], 'Admin.Orderscustomers.Feature'),
                    'input' => 'id',
                    'autocomplete' => true,
                    'constraints' => $this->createNotBlankConstraint(),
                ])
                ->add('role_id', B2bRoleChoiceType::class, [
                    'label' => $this->translator->trans('Role', [], 'Admin.Orderscustomers.Feature'),
                    'placeholder' => $this->translator->trans('Select a role', [], 'Admin.Orderscustomers.Feature'),
                    'help' => $this->translator->trans('The role defines the contact\'s permissions within the business entity. Only administrators can manage the user list, access all the invoices of the organization and modify the company details. Other roles, such as buyers, can only place orders and view their own orders.', [], 'Admin.Orderscustomers.Help'),
                    'input' => 'id',
                    'constraints' => $this->createNotBlankConstraint(),
                ]);
        }

        $builder
            ->add('external_ref', TextType::class, [
                'label' => $this->translator->trans('External Reference', [], 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => ExternalReference::MAX_LENGTH,
                        'maxMessage' => $this->translator->trans('This field cannot be longer than %limit% characters', [
                            '%limit%' => ExternalReference::MAX_LENGTH,
                        ], 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('status', EnumType::class, [
                'label' => $this->translator->trans('Status', [], 'Admin.Global'),
                'class' => CustomerB2bStatus::class,
                'constraints' => $this->createNotBlankConstraint(),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'is_edit' => false,
            ])
            ->setAllowedTypes('is_edit', 'bool');
    }

    private static function usesExistingCustomer(FormInterface $form): bool
    {
        return (bool) $form->getParent()?->get('use_existing_customer')->getData();
    }

    private function createNotBlankConstraint(): NotBlank
    {
        return new NotBlank([
            'message' => $this->translator->trans('This field cannot be empty.', [], 'Admin.Notifications.Error'),
        ]);
    }
}
