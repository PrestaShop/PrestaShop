<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\BusinessEntity;

use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\GroupByIdChoiceProvider;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * How the company is referenced and classified. Declared with inherit_data so the submitted data
 * stays flat under the general information section; the nesting only exists to lay the fields out.
 */
class BusinessEntitySettingsType extends TranslatorAwareType
{
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
            ->add(BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF, TextType::class, [
                'label' => $this->trans('External Reference', 'Admin.Global'),
                'required' => false,
                // Keeps the field alone on its line, the way the product page breaks a column run.
                'column_breaker' => true,
            ])
            ->add(BusinessEntityGeneralInformationType::FIELD_STATUS, EnumType::class, [
                'label' => $this->trans('Status', 'Admin.Global'),
                'class' => BusinessEntityStatus::class,
                'constraints' => [new NotBlank()],
            ])
            ->add(BusinessEntityGeneralInformationType::FIELD_CUSTOMER_GROUP_ID, ChoiceType::class, [
                'label' => $this->trans('Customer group', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Customer group attached to this business entity.', 'Admin.Catalog.Feature'),
                'choices' => $this->groupByIdChoiceProvider->getChoices(),
                'required' => true,
                'constraints' => [new NotBlank()],
                'column_breaker' => true,
            ])
            ->add(BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED, SwitchType::class, [
                'label' => $this->trans('Delivery authorized', 'Admin.Global'),
                'help' => $this->trans('Allow the B2B customer to order using an address that does not belong to the business entity.', 'Admin.Catalog.Feature'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('inherit_data', true);
    }
}
