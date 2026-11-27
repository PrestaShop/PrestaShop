<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Customer;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CustomerName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email as DomainEmail;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CustomerBasicDataType extends TranslatorAwareType
{
    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct($translator, []);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('gender_id', GenderType::class, [
                'expanded' => true,
                'required' => false,
                'placeholder' => null,
            ])
            ->add('first_name', TextType::class, [
                'label' => $this->trans('First name', 'Admin.Global'),
                'help' => $this->trans(
                    'Only letters and the dot (.) character, followed by a space, are allowed.',
                    'Admin.Orderscustomers.Help'
                ),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
                    ]),
                    new Length([
                        'max' => FirstName::MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => FirstName::MAX_LENGTH]
                        ),
                    ]),
                    new CustomerName([
                        'message' => $this->trans('The %s field is invalid.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('First name', 'Admin.Global'))]
                        ),
                    ]),
                ],
            ])
            ->add('last_name', TextType::class, [
                'label' => $this->trans('Last name', 'Admin.Global'),
                'help' => $this->trans(
                    'Only letters and the dot (.) character, followed by a space, are allowed.',
                    'Admin.Orderscustomers.Help'
                ),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
                    ]),
                    new Length([
                        'max' => LastName::MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => LastName::MAX_LENGTH]
                        ),
                    ]),
                    new CustomerName([
                        'message' => $this->trans(
                            'The %s field is invalid.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Last name', 'Admin.Global'))]
                        ),
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => $this->trans('Email', 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
                    ]),
                    new Length([
                        'max' => DomainEmail::MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters.',
                            'Admin.Notifications.Error',
                            ['%limit%' => DomainEmail::MAX_LENGTH]
                        ),
                    ]),
                    new Email([
                        'message' => $this->trans('This field is invalid.', 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
        ;
    }
}
