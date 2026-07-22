<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\BusinessEntity;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\GroupByIdChoiceProvider;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class BusinessEntityGeneralInformationType extends TranslatorAwareType
{
    public const FIELD_NAME = 'name';
    public const FIELD_LEGAL_NAME = 'legal_name';
    public const FIELD_EXTERNAL_REF = 'external_ref';
    public const FIELD_DELIVERY_AUTHORIZED = 'delivery_authorized';
    public const FIELD_STATUS = 'status';
    public const FIELD_CUSTOMER_GROUP_ID = 'customer_group_id';

    public const MAX_NAME_LENGTH = 255;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly GroupByIdChoiceProvider $groupByIdChoiceProvider,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::FIELD_NAME, TextType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'help' => $this->trans('The display name of the business entity.', 'Admin.Catalog.Feature'),
                'constraints' => $this->getNameConstraints(),
                'required' => true,
            ])
            ->add(self::FIELD_LEGAL_NAME, TextType::class, [
                'label' => $this->trans('Legal Name', 'Admin.Global'),
                'help' => $this->trans('The official registered name of the company.', 'Admin.Catalog.Feature'),
                'constraints' => $this->getNameConstraints(),
                'required' => true,
            ])
            ->add(self::FIELD_EXTERNAL_REF, TextType::class, [
                'label' => $this->trans('External Reference', 'Admin.Global'),
                'required' => false,
            ])
            ->add(self::FIELD_DELIVERY_AUTHORIZED, SwitchType::class, [
                'label' => $this->trans('Delivery authorized', 'Admin.Global'),
                'help' => $this->trans('Allow the B2B customer to order using an address that does not belong to the business entity.', 'Admin.Catalog.Feature'),
            ])
            ->add(self::FIELD_STATUS, EnumType::class, [
                'label' => $this->trans('Status', 'Admin.Global'),
                'class' => BusinessEntityStatus::class,
            ])
            ->add(self::FIELD_CUSTOMER_GROUP_ID, ChoiceType::class, [
                'label' => $this->trans('Customer group', 'Admin.Global'),
                'help' => $this->trans('Customer group attached to this business entity.', 'Admin.Catalog.Feature'),
                'choices' => $this->groupByIdChoiceProvider->getChoices(),
                'required' => true,
            ]);
    }

    /**
     * @return array<int, Constraint>
     */
    private function getNameConstraints(): array
    {
        return [
            new NotBlank([
                'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
            ]),
            new Length([
                'max' => self::MAX_NAME_LENGTH,
                'maxMessage' => $this->trans(
                    'This field cannot be longer than %limit% characters',
                    'Admin.Notifications.Error',
                    ['%limit%' => self::MAX_NAME_LENGTH]
                ),
            ]),
            new TypedRegex([
                'type' => TypedRegex::TYPE_GENERIC_NAME,
            ]),
            new CleanHtml([
                'message' => $this->trans('%s is invalid.', 'Admin.Notifications.Error'),
            ]),
        ];
    }
}
